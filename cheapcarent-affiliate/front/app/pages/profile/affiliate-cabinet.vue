<template>
  <section class="affiliate-cabinet">
    <header class="profile-library__header">
      <span class="profile-library__eyebrow">CheapCarRent</span>
      <h1 class="profile__title">{{ copy.title }}</h1>
      <p class="profile__subtitle">{{ copy.intro }}</p>
    </header>

    <div v-if="loadFailed" class="affiliate-cabinet__notice" role="alert">
      {{ copy.loadError }}
      <button type="button" @click="loadCabinet">{{ copy.retry }}</button>
    </div>

    <div v-else class="affiliate-cabinet__dashboard">
      <div class="affiliate-cabinet__status">
        <div>
          <span class="affiliate-cabinet__eyebrow">{{ copy.account }}</span>
          <h2>{{ partner?.display_name || copy.applicationTitle }}</h2>
          <p>{{ partner?.website_url || copy.applicationIntro }}</p>
        </div>
        <span class="affiliate-cabinet__badge">{{ partner ? statusLabel : copy.notAppliedLabel }}</span>
      </div>

      <div v-if="!partner" class="affiliate-cabinet__notice" role="status">
        {{ copy.notAppliedNotice }} <button type="button" @click="activeTab = 'profile'">{{ copy.submit }}</button>
      </div>
      <div v-else-if="partner.status === 'pending'" class="affiliate-cabinet__notice" role="status">
        {{ copy.pending }}
      </div>
      <div v-else-if="partner.status === 'rejected'" class="affiliate-cabinet__notice" role="status">
        {{ copy.rejected }}
      </div>

      <nav class="affiliate-cabinet__tabs" :aria-label="copy.navigation">
        <button v-for="item in tabs" :key="item" type="button" :class="[`affiliate-cabinet__tab--${item}`, { 'is-active': activeTab === item }]"
          :aria-current="activeTab === item ? 'page' : undefined" @click="activeTab = item">
          <span class="affiliate-cabinet__nav-icon"><AffiliateCabinetIcon :name="item" /></span>
          {{ copy[`${item}Tab`] }}
        </button>
      </nav>

      <div v-if="activeTab === 'reports'" class="affiliate-cabinet__panel">
        <div class="affiliate-cabinet__panel-heading">
          <h3>{{ copy.reportsTitle }}</h3>
          <p>{{ copy.reportsIntro }}</p>
        </div>
        <div v-if="partner?.status === 'approved'" class="affiliate-cabinet__metric-grid">
          <article class="affiliate-cabinet__metric"><span>{{ copy.clicksMetric }}</span><strong>{{ report?.clicks ?? '—' }}</strong><small>{{ copy.lastThirtyDays }}</small></article>
          <article class="affiliate-cabinet__metric"><span>{{ copy.ordersMetric }}</span><strong>{{ report?.orders ?? '—' }}</strong><small>{{ copy.lastThirtyDays }}</small></article>
          <article class="affiliate-cabinet__metric"><span>{{ copy.paidOrdersMetric }}</span><strong>{{ report?.paid_orders ?? '—' }}</strong><small>{{ copy.lastThirtyDays }}</small></article>
        </div>
        <div class="affiliate-cabinet__metric-grid">
          <article v-for="metric in ['earned', 'ready', 'awaiting']" :key="metric" class="affiliate-cabinet__metric">
            <span class="affiliate-cabinet__metric-top"><span class="affiliate-cabinet__icon-tile"><AffiliateCabinetIcon :name="metric" /></span>{{ copy[`${metric}Metric`] }}</span><strong>—</strong><small>{{ copy.dataUnavailable }}</small>
          </article>
        </div>
        <article class="affiliate-cabinet__surface affiliate-cabinet__chart-card">
          <div class="affiliate-cabinet__surface-head"><div><h4 class="affiliate-cabinet__icon-heading"><span class="affiliate-cabinet__icon-tile"><AffiliateCabinetIcon name="chart" /></span>{{ copy.chartTitle }}</h4><p>{{ copy.chartIntro }}</p></div><span class="affiliate-cabinet__year">{{ new Date().getFullYear() }}</span></div>
          <div class="affiliate-cabinet__chart" role="img" :aria-label="copy.chartEmpty">
            <div class="affiliate-cabinet__gridlines" aria-hidden="true"><i v-for="line in 4" :key="line" /></div>
            <span>{{ copy.chartEmpty }}</span>
          </div>
          <div class="affiliate-cabinet__months" aria-hidden="true"><span v-for="month in copy.months" :key="month">{{ month }}</span></div>
          <div class="affiliate-cabinet__legend"><span>{{ copy.earningsLegend }}</span><span>{{ copy.visitsLegend }}</span></div>
        </article>
        <div class="affiliate-cabinet__report-grid">
          <article class="affiliate-cabinet__surface"><h4 class="affiliate-cabinet__icon-heading"><span class="affiliate-cabinet__icon-tile"><AffiliateCabinetIcon name="payout" /></span>{{ copy.payoutTitle }}</h4><p>{{ copy.payoutUnavailable }}</p><button type="button" disabled>{{ copy.withdraw }}</button></article>
          <article class="affiliate-cabinet__surface"><h4 class="affiliate-cabinet__icon-heading"><span class="affiliate-cabinet__icon-tile"><AffiliateCabinetIcon name="awaiting" /></span>{{ copy.pendingTitle }}</h4><p>{{ copy.pendingUnavailable }}</p></article>
        </div>
        <article v-for="table in ['orders', 'payouts']" :key="table" class="affiliate-cabinet__surface">
          <div class="affiliate-cabinet__surface-head"><h4 class="affiliate-cabinet__icon-heading"><span class="affiliate-cabinet__icon-tile"><AffiliateCabinetIcon :name="table" /></span>{{ copy[`${table}Title`] }}</h4><button type="button" disabled>{{ copy.export }}</button></div>
          <div class="affiliate-cabinet__table-scroll"><table><thead><tr><th v-for="heading in copy[`${table}Columns`]" :key="heading" scope="col">{{ heading }}</th></tr></thead><tbody><tr><td :colspan="copy[`${table}Columns`].length" class="affiliate-cabinet__empty">{{ copy.tableEmpty }}</td></tr></tbody></table></div>
        </article>
      </div>

      <div v-else-if="activeTab === 'tools'" class="affiliate-cabinet__panel">
        <div class="affiliate-cabinet__panel-heading"><h3>{{ copy.toolsTitle }}</h3><p>{{ copy.toolsIntro }}</p></div>
        <article class="affiliate-cabinet__surface affiliate-cabinet__tool-builder"><div><h4 class="affiliate-cabinet__icon-heading"><span class="affiliate-cabinet__icon-tile"><AffiliateCabinetIcon name="links" /></span>{{ copy.linksTitle }}</h4><p>{{ partner?.status === 'approved' ? copy.linksReady : copy.linksUnavailable }}</p></div>
          <form class="affiliate-cabinet__tool-fields" @submit.prevent="createLink"><label>{{ copy.destinationLabel }}<input v-model.trim="linkDestination" type="text" :disabled="partner?.status !== 'approved'" placeholder="/"></label><label>{{ copy.markerLabel }}<input v-model.trim="linkMarker" type="text" :disabled="partner?.status !== 'approved'" :placeholder="copy.markerPlaceholder"></label><button type="submit" :disabled="partner?.status !== 'approved' || linkBusy">{{ copy.createLink }}</button></form>
          <p v-if="linkError" class="affiliate-cabinet__error" role="alert">{{ linkError }}</p>
          <div v-if="linkUrl" class="affiliate-cabinet__generated-link"><label>{{ copy.generatedLink }}<input :value="linkUrl" type="text" readonly @focus="$event.target.select()"></label><a :href="linkUrl" target="_blank" rel="noopener noreferrer">{{ copy.openLink }}</a></div>
        </article>
        <div class="affiliate-cabinet__report-grid">
          <article class="affiliate-cabinet__surface"><h4 class="affiliate-cabinet__icon-heading"><span class="affiliate-cabinet__icon-tile"><AffiliateCabinetIcon name="widgets" /></span>{{ copy.widgetsTitle }}</h4><p>{{ copy.widgetsUnavailable }}</p><span class="affiliate-cabinet__coming">{{ copy.comingSoon }}</span></article>
          <article class="affiliate-cabinet__surface"><h4 class="affiliate-cabinet__icon-heading"><span class="affiliate-cabinet__icon-tile"><AffiliateCabinetIcon name="whiteLabel" /></span>White Label</h4><p>{{ copy.whiteLabelUnavailable }}</p><span class="affiliate-cabinet__coming">{{ copy.comingSoon }}</span></article>
        </div>
      </div>

      <div v-else class="affiliate-cabinet__panel affiliate-cabinet__profile">
        <div class="affiliate-cabinet__panel-heading"><h3>{{ partner ? copy.profileTitle : copy.applicationTitle }}</h3><p>{{ partner ? copy.profileIntro : copy.applicationIntro }}</p></div>
        <form v-if="!partner" class="affiliate-cabinet__form" @submit.prevent="apply">
          <label><span>{{ copy.name }}</span><input v-model.trim="form.display_name" required minlength="2" maxlength="160" autocomplete="organization"></label>
          <label><span>{{ copy.website }}</span><input v-model.trim="form.website_url" required type="url" maxlength="500" placeholder="https://example.com" autocomplete="url"></label>
          <label><span>{{ copy.promotion }}</span><textarea v-model.trim="form.promotion_description" required minlength="20" maxlength="1000" rows="4" /></label>
          <p v-if="submitError" class="affiliate-cabinet__error" role="alert">{{ submitError }}</p>
          <button class="affiliate-cabinet__button" type="submit" :disabled="submitting">{{ submitting ? copy.sending : copy.submit }}</button>
        </form>
        <dl v-else>
          <div><dt>{{ copy.name }}</dt><dd>{{ partner.display_name }}</dd></div>
          <div><dt>{{ copy.website }}</dt><dd>{{ partner.website_url }}</dd></div>
          <div><dt>{{ copy.promotion }}</dt><dd>{{ partner.promotion_description }}</dd></div>
          <div><dt>{{ copy.applicationStatus }}</dt><dd>{{ statusLabel }}</dd></div>
        </dl>
      </div>
    </div>
  </section>
</template>

<script>
definePageMeta({ layout: 'profile', middleware: ['affiliate-auth'] })

import noindexMeta from '~/mixins/noindex'
import AffiliateCabinetIcon from '~/components/affiliate/CabinetIcon.vue'

const copies = {
  ru: {
    title: 'Кабинет аффилиата', intro: 'Заявка и статус участия в партнёрской программе CheapCarRent.',
    loadError: 'Не удалось загрузить кабинет.', retry: 'Повторить',
    applicationTitle: 'Стать партнёром', applicationIntro: 'Расскажите о площадке. Мы проверим заявку перед включением партнёрских ссылок.',
    name: 'Название площадки или организации', website: 'Сайт или публичная страница',
    promotion: 'Как вы планируете рассказывать об аренде автомобилей?',
    submit: 'Отправить заявку', sending: 'Отправка…', submitError: 'Не удалось отправить заявку. Проверьте данные и попробуйте ещё раз.',
    account: 'Партнёрская заявка', pendingLabel: 'На рассмотрении', approvedLabel: 'Одобрена', rejectedLabel: 'Отклонена',
    pending: 'Заявка получена. Ссылки и статистика появятся после запуска учёта партнёрских заказов.',
    rejected: 'Заявка не одобрена. Для уточнения причины обратитесь в поддержку CheapCarRent.',
    linksTitle: 'Партнёрские ссылки', linksUnavailable: 'Ссылки доступны после одобрения заявки.', linksReady: 'Создайте ссылку на страницу CheapCarRent. Бронирования основного каталога учитываются после перехода.',
    clicksMetric: 'Переходы', ordersMetric: 'Заказы', paidOrdersMetric: 'Оплаченные заказы', lastThirtyDays: 'Последние 30 дней', generatedLink: 'Ваша ссылка', openLink: 'Открыть', linkError: 'Не удалось создать ссылку. Укажите внутренний путь, например /.',
    statisticsTitle: 'Статистика', statisticsUnavailable: 'Учёт переходов и бронирований ещё не подключён.',
    commissionsTitle: 'Начисления и выплаты', commissionsUnavailable: 'Подтверждённых начислений и выплат пока нет.',
    navigation: 'Меню партнёра', reportsTab: 'Отчёты', toolsTab: 'Инструменты', profileTab: 'Профиль',
    reportsTitle: 'Отчёты', reportsIntro: 'Переходы, бронирования и комиссии появятся после запуска партнёрского учёта.',
    pendingTitle: 'Ожидают завершения', pendingUnavailable: 'Заказы с ожидаемой комиссией пока не отображаются.',
    toolsTitle: 'Инструменты', toolsIntro: 'Инструменты продвижения будут доступны после запуска отслеживания заказов.',
    markersTitle: 'Метки размещений', markersUnavailable: 'Метки для разных площадок пока не подключены.',
    widgetsTitle: 'Виджеты', widgetsUnavailable: 'Поисковый виджет и White Label пока не подключены.',
    profileTitle: 'Профиль партнёра', profileIntro: 'Данные отправленной заявки. Изменение данных пока выполняется через поддержку.',
    applicationStatus: 'Статус заявки',
    notAppliedLabel: 'Заявка не подана', notAppliedNotice: 'Заполните заявку, чтобы стать партнёром. Разделы кабинета уже доступны для ознакомления.',
    earnedMetric: 'Всего начислено', readyMetric: 'Доступно к выплате', awaitingMetric: 'Ожидает завершения', dataUnavailable: 'Учёт пока не запущен',
    chartTitle: 'Динамика программы', chartIntro: 'Начисления и переходы по месяцам', chartEmpty: 'График появится после запуска отслеживания',
    months: ['Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн', 'Июл', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек'], earningsLegend: 'Начисления', visitsLegend: 'Переходы',
    payoutTitle: 'Вывод средств', payoutUnavailable: 'Способы и условия выплат будут опубликованы перед запуском программы.', withdraw: 'Запросить выплату',
    ordersTitle: 'Партнёрские бронирования', payoutsTitle: 'История выплат', ordersColumns: ['Статус', 'Дата', 'Заказ', 'Автомобиль', 'Аренда', 'Сумма', 'Комиссия'], payoutsColumns: ['Дата', 'Способ', 'Сумма', 'Статус'],
    tableEmpty: 'Данные пока недоступны', export: 'Экспорт', destinationLabel: 'Страница назначения', markerLabel: 'Метка размещения', markerPlaceholder: 'Например, blog', createLink: 'Создать ссылку', comingSoon: 'Скоро',
    whiteLabelUnavailable: 'Встраиваемая форма поиска пока не подключена.',
  },
  en: {
    title: 'Affiliate dashboard', intro: 'Your application and status in the CheapCarRent affiliate program.',
    loadError: 'The dashboard could not be loaded.', retry: 'Retry',
    applicationTitle: 'Become an affiliate', applicationIntro: 'Tell us about your website. We will review your application before enabling affiliate links.',
    name: 'Website or organisation name', website: 'Website or public page',
    promotion: 'How will you promote car rentals?',
    submit: 'Send application', sending: 'Sending…', submitError: 'The application could not be sent. Check your details and try again.',
    account: 'Affiliate application', pendingLabel: 'Under review', approvedLabel: 'Approved', rejectedLabel: 'Declined',
    pending: 'Application received. Links and statistics will appear when affiliate order tracking launches.',
    rejected: 'Your application was declined. Contact CheapCarRent support for details.',
    linksTitle: 'Affiliate links', linksUnavailable: 'Links are available after approval.', linksReady: 'Create a link to a CheapCarRent page. Core catalogue bookings are attributed after a visit.',
    clicksMetric: 'Visits', ordersMetric: 'Orders', paidOrdersMetric: 'Paid orders', lastThirtyDays: 'Last 30 days', generatedLink: 'Your link', openLink: 'Open', linkError: 'Could not create the link. Enter an internal path such as /.',
    statisticsTitle: 'Statistics', statisticsUnavailable: 'Click and booking tracking is not available yet.',
    commissionsTitle: 'Commissions and payouts', commissionsUnavailable: 'There are no confirmed commissions or payouts yet.',
    navigation: 'Affiliate navigation', reportsTab: 'Reports', toolsTab: 'Tools', profileTab: 'Profile',
    reportsTitle: 'Reports', reportsIntro: 'Visits, bookings and commissions will appear when affiliate tracking launches.',
    pendingTitle: 'Awaiting completion', pendingUnavailable: 'Orders with pending commission are not shown yet.',
    toolsTitle: 'Tools', toolsIntro: 'Promotion tools will be available when booking attribution launches.',
    markersTitle: 'Placement markers', markersUnavailable: 'Markers for different placements are not available yet.',
    widgetsTitle: 'Widgets', widgetsUnavailable: 'The search widget and White Label are not available yet.',
    profileTitle: 'Affiliate profile', profileIntro: 'Details from your application. Contact support to request a change.',
    applicationStatus: 'Application status',
    notAppliedLabel: 'Not applied', notAppliedNotice: 'Send an application to join the program. You can already explore the dashboard sections.',
    earnedMetric: 'Total earned', readyMetric: 'Ready for payout', awaitingMetric: 'Awaiting completion', dataUnavailable: 'Tracking has not launched',
    chartTitle: 'Program activity', chartIntro: 'Earnings and visits by month', chartEmpty: 'The chart will appear after tracking launches',
    months: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'], earningsLegend: 'Earnings', visitsLegend: 'Visits',
    payoutTitle: 'Withdraw funds', payoutUnavailable: 'Payout methods and terms will be published before the program launches.', withdraw: 'Request payout',
    ordersTitle: 'Affiliate bookings', payoutsTitle: 'Payout history', ordersColumns: ['Status', 'Date', 'Order', 'Vehicle', 'Rental', 'Amount', 'Commission'], payoutsColumns: ['Date', 'Method', 'Amount', 'Status'],
    tableEmpty: 'Data is not available yet', export: 'Export', destinationLabel: 'Destination page', markerLabel: 'Placement marker', markerPlaceholder: 'For example, blog', createLink: 'Create link', comingSoon: 'Coming soon',
    whiteLabelUnavailable: 'The embedded search form is not available yet.',
  },
}

export default defineNuxtComponent({
  components: { AffiliateCabinetIcon },
  mixins: [noindexMeta],
  layout: 'profile',
  async asyncData({ $axios }) {
    try {
      const data = await $axios.$get('/api/v1/affiliate/cabinet')
      return { partner: data.partner, loadFailed: false }
    } catch (_) {
      return { partner: null, loadFailed: true }
    }
  },
  data() {
    return {
      partner: null,
      loadFailed: false,
      submitting: false,
      submitError: '',
      activeTab: 'reports',
      tabs: ['reports', 'tools', 'profile'],
      form: { display_name: '', website_url: '', promotion_description: '' },
      report: null,
      linkDestination: '/',
      linkMarker: '',
      linkUrl: '',
      linkError: '',
      linkBusy: false,
    }
  },
  computed: {
    copy() {
      const locale = this.$i18n?.locale?.value || this.$i18n?.locale || 'en'
      return copies[locale] || copies.en
    },
    statusLabel() {
      return this.copy[`${this.partner?.status || 'pending'}Label`] || this.copy.pendingLabel
    },
  },
  methods: {
    async loadReport() {
      if (this.partner?.status !== 'approved') return
      try { this.report = await this.$axios.$get('/api/v1/affiliate/report') } catch (_) { this.report = null }
    },
    async createLink() {
      if (this.partner?.status !== 'approved' || this.linkBusy) return
      this.linkBusy = true
      this.linkError = ''
      this.linkUrl = ''
      try {
        const data = await this.$axios.$get('/api/v1/affiliate/tools', { params: { to: this.linkDestination || '/', sub_id: this.linkMarker || undefined } })
        this.linkUrl = data.url
      } catch (_) {
        this.linkError = this.copy.linkError
      } finally {
        this.linkBusy = false
      }
    },
    async loadCabinet() {
      try {
        const data = await this.$axios.$get('/api/v1/affiliate/cabinet')
        this.partner = data.partner
        this.loadFailed = false
        await this.loadReport()
      } catch (_) {
        this.loadFailed = true
      }
    },
    async apply() {
      if (this.submitting) return
      this.submitting = true
      this.submitError = ''
      try {
        const data = await this.$axios.$post('/api/v1/affiliate/apply', this.form)
        this.partner = data.partner
      } catch (error) {
        if (error?.response?.status === 409) await this.loadCabinet()
        else this.submitError = this.copy.submitError
      } finally {
        this.submitting = false
      }
    },
  },
  mounted() { this.loadReport() },
})
</script>

<style scoped>
.affiliate-cabinet { color: #102a4c; }
.affiliate-cabinet__form, .affiliate-cabinet__status, .affiliate-cabinet__sections article {
  padding: 25px; border: 1px solid #d8e7f3; border-radius: 20px; background: #fff;
}
.affiliate-cabinet__form { display: grid; gap: 18px; max-width: 690px; }
.affiliate-cabinet__form h2, .affiliate-cabinet__status h2 { margin: 0; font-size: 24px; }
.affiliate-cabinet__form p, .affiliate-cabinet__status p, .affiliate-cabinet__sections p { margin: 0; color: #5d6f83; line-height: 1.5; }
.affiliate-cabinet__form label { display: grid; gap: 7px; font-weight: 650; }
.affiliate-cabinet__form input, .affiliate-cabinet__form textarea { width: 100%; padding: 12px 14px; border: 1px solid #b6cfe2; border-radius: 11px; background: #fff; color: #102a4c; font: inherit; }
.affiliate-cabinet__form input:focus, .affiliate-cabinet__form textarea:focus { outline: 3px solid #c7e6ff; border-color: #137ac9; }
.affiliate-cabinet__button { justify-self: start; padding: 13px 20px; border: 0; border-radius: 11px; color: #fff; background: #137ac9; font-weight: 700; cursor: pointer; }
.affiliate-cabinet__button:disabled { opacity: .55; cursor: wait; }
.affiliate-cabinet__notice { padding: 18px 20px; border-radius: 14px; background: #eef7ff; color: #244a6b; line-height: 1.5; }
.affiliate-cabinet__notice button { margin-left: 12px; color: #075a9a; border: 0; background: none; text-decoration: underline; cursor: pointer; }
.affiliate-cabinet__error { color: #a32525 !important; }
.affiliate-cabinet__dashboard { display: grid; grid-template-columns: 210px minmax(0, 1fr); gap: 20px; align-items: start; }
.affiliate-cabinet__status, .affiliate-cabinet__notice { grid-column: 1 / -1; }
.affiliate-cabinet__tabs { display: grid; gap: 6px; padding: 12px; border: 1px solid #d8e7f3; border-radius: 18px; background: #fff; min-width: 0; }
.affiliate-cabinet__tabs button { display: flex; align-items: center; gap: 12px; width: 100%; padding: 9px 11px; border: 0; border-radius: 11px; color: #223b58; background: transparent; font: inherit; font-weight: 700; text-align: left; cursor: pointer; }
.affiliate-cabinet__tabs button:hover, .affiliate-cabinet__tabs button.is-active { color: #102a4c; background: #edf5fb; }
.affiliate-cabinet__nav-icon { display: grid; place-items: center; width: 40px; height: 40px; flex: none; border-radius: 12px; color: #fff; box-shadow: 0 4px 10px #17396325; }
.affiliate-cabinet__tab--reports .affiliate-cabinet__nav-icon { background: linear-gradient(145deg, #28a7f2, #1e67ca); }
.affiliate-cabinet__tab--tools .affiliate-cabinet__nav-icon { background: linear-gradient(145deg, #a66af3, #6040c8); }
.affiliate-cabinet__tab--profile .affiliate-cabinet__nav-icon { background: linear-gradient(145deg, #42cfaf, #11967b); }
.affiliate-cabinet__tabs button:focus-visible { outline: 3px solid #76c3fa; outline-offset: 2px; }
.affiliate-cabinet__panel { display: grid; gap: 18px; grid-column: 2; min-width: 0; }
.affiliate-cabinet__panel-heading h3 { margin: 0 0 6px; font-size: 22px; }
.affiliate-cabinet__panel-heading p { margin: 0; color: #5d6f83; line-height: 1.5; }
.affiliate-cabinet__profile { padding: 25px; border: 1px solid #d8e7f3; border-radius: 20px; background: #fff; }
.affiliate-cabinet__profile .affiliate-cabinet__form { padding: 0; border: 0; border-radius: 0; max-width: 690px; }
.affiliate-cabinet__profile dl { display: grid; gap: 15px; margin: 0; }
.affiliate-cabinet__profile dl div { padding-top: 14px; border-top: 1px solid #e5eef5; }
.affiliate-cabinet__profile dt { margin-bottom: 4px; color: #5d6f83; font-size: 13px; }
.affiliate-cabinet__profile dd { margin: 0; overflow-wrap: anywhere; }
.affiliate-cabinet__status { display: flex; justify-content: space-between; gap: 20px; align-items: start; }
.affiliate-cabinet__status p { margin-top: 7px; overflow-wrap: anywhere; }
.affiliate-cabinet__eyebrow { color: #137ac9; font-size: 12px; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; }
.affiliate-cabinet__badge { flex: none; padding: 8px 12px; border-radius: 999px; background: #fff3cf; color: #785400; font-size: 13px; font-weight: 700; }
.affiliate-cabinet__sections { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 14px; }
.affiliate-cabinet__sections article { min-width: 0; }
.affiliate-cabinet__sections h3 { margin: 12px 0 8px; font-size: 18px; }
.affiliate-cabinet__sections h4 { margin: 12px 0 8px; font-size: 18px; }
.affiliate-cabinet__metric-grid, .affiliate-cabinet__report-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 14px; }
.affiliate-cabinet__report-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
.affiliate-cabinet__metric, .affiliate-cabinet__surface { min-width: 0; padding: 23px; border: 1px solid #d8e7f3; border-radius: 18px; background: #fff; }
.affiliate-cabinet__metric { display: grid; gap: 8px; box-shadow: 0 8px 25px #12355308; }
.affiliate-cabinet__metric span { color: #526982; font-size: 14px; font-weight: 700; }
.affiliate-cabinet__metric-top, .affiliate-cabinet__icon-heading { display: flex; align-items: center; gap: 11px; }
.affiliate-cabinet__metric-top { margin-bottom: 2px; }
.affiliate-cabinet__icon-tile { display: inline-grid; place-items: center; width: 38px; height: 38px; flex: none; border: 1px solid #d6eaf8; border-radius: 12px; background: linear-gradient(145deg, #f1faff, #deeffb); color: #137ac9 !important; box-shadow: 0 3px 8px #1477b713; }
.affiliate-cabinet__icon-heading { min-height: 38px; }
.affiliate-cabinet__metric strong { color: #102a4c; font-size: 30px; line-height: 1; }
.affiliate-cabinet__metric small { color: #7b8da1; }
.affiliate-cabinet__surface h4 { margin: 0 0 8px; font-size: 18px; }
.affiliate-cabinet__surface p { margin: 0; color: #60758b; line-height: 1.5; }
.affiliate-cabinet__surface-head { display: flex; align-items: start; justify-content: space-between; gap: 16px; }
.affiliate-cabinet__year, .affiliate-cabinet__coming { flex: none; padding: 7px 11px; border-radius: 9px; background: #edf5fb; color: #176fac; font-size: 13px; font-weight: 750; }
.affiliate-cabinet__coming { display: inline-block; margin-top: 18px; }
.affiliate-cabinet__chart { position: relative; display: grid; place-items: center; min-height: 220px; margin-top: 22px; overflow: hidden; border-bottom: 1px solid #c9dbe9; color: #60758b; text-align: center; }
.affiliate-cabinet__gridlines { position: absolute; inset: 0; display: flex; flex-direction: column; justify-content: space-around; pointer-events: none; }
.affiliate-cabinet__gridlines i { width: 100%; border-top: 1px dashed #e4edf5; }
.affiliate-cabinet__chart > span { z-index: 1; padding: 10px 15px; border-radius: 10px; background: #fff; }
.affiliate-cabinet__months { display: grid; grid-template-columns: repeat(12, 1fr); gap: 2px; padding-top: 10px; color: #8092a5; font-size: 11px; text-align: center; }
.affiliate-cabinet__legend { display: flex; gap: 18px; margin-top: 18px; color: #526982; font-size: 13px; }
.affiliate-cabinet__legend span::before { content: ''; display: inline-block; width: 9px; height: 9px; margin-right: 7px; border-radius: 50%; background: #167bca; }
.affiliate-cabinet__legend span + span::before { background: #ffc043; }
.affiliate-cabinet__surface button { margin-top: 18px; padding: 10px 15px; border: 1px solid #d2e2ee; border-radius: 9px; background: #f1f6fa; color: #788ca0; font: inherit; font-weight: 700; cursor: not-allowed; }
.affiliate-cabinet__surface-head button { margin-top: 0; }
.affiliate-cabinet__table-scroll { margin-top: 16px; overflow-x: auto; }
.affiliate-cabinet__surface table { width: 100%; border-collapse: collapse; text-align: left; font-size: 13px; }
.affiliate-cabinet__surface th { padding: 12px 10px; border-bottom: 1px solid #d8e7f3; color: #526982; white-space: nowrap; }
.affiliate-cabinet__empty { padding: 28px 10px; color: #788ca0; text-align: center; }
.affiliate-cabinet__tool-builder { display: grid; gap: 20px; }
.affiliate-cabinet__tool-fields { display: grid; grid-template-columns: minmax(0, 2fr) minmax(0, 1fr) auto; gap: 12px; align-items: end; }
.affiliate-cabinet__tool-fields label { display: grid; gap: 7px; font-size: 13px; font-weight: 700; }
.affiliate-cabinet__tool-fields input { width: 100%; min-width: 0; padding: 11px; border: 1px solid #d2e2ee; border-radius: 9px; background: #f7fafc; color: #788ca0; font: inherit; }
.affiliate-cabinet__tool-fields button { margin-top: 0; white-space: nowrap; }
.affiliate-cabinet__tool-fields button:not(:disabled) { background: #137ac9; border-color: #137ac9; color: #fff; cursor: pointer; }
.affiliate-cabinet__tool-fields input:not(:disabled) { background: #fff; color: #102a4c; }
.affiliate-cabinet__generated-link { display: flex; align-items: end; gap: 14px; min-width: 0; }
.affiliate-cabinet__generated-link label { display: grid; gap: 7px; flex: 1; min-width: 0; font-size: 13px; font-weight: 700; }
.affiliate-cabinet__generated-link input { width: 100%; min-width: 0; padding: 11px; border: 1px solid #d2e2ee; border-radius: 9px; font: inherit; }
.affiliate-cabinet__generated-link a { color: #137ac9; font-weight: 700; }
@media (max-width: 800px) { .affiliate-cabinet__sections { grid-template-columns: 1fr; } .affiliate-cabinet__dashboard { grid-template-columns: 1fr; } .affiliate-cabinet__tabs { display: flex; flex-wrap: wrap; } .affiliate-cabinet__tabs button { width: auto; } .affiliate-cabinet__panel { grid-column: 1; } }
@media (max-width: 800px) { .affiliate-cabinet__metric-grid, .affiliate-cabinet__report-grid { grid-template-columns: 1fr; } .affiliate-cabinet__tool-fields { grid-template-columns: 1fr; } }
@media (max-width: 580px) { .affiliate-cabinet__months span:nth-child(even) { visibility: hidden; } .affiliate-cabinet__metric, .affiliate-cabinet__surface { padding: 18px; } }
@media (max-width: 500px) { .affiliate-cabinet__status { flex-direction: column; } .affiliate-cabinet__form, .affiliate-cabinet__status, .affiliate-cabinet__sections article { padding: 19px; } }
</style>
