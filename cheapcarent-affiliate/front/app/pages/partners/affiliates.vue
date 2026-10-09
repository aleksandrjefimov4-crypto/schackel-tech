<template>
  <div class="affiliate-page">
    <div class="affiliate-wrap">
      <nav class="affiliate-crumbs" :aria-label="copy.breadcrumb">
        <NuxtLink :to="localePath('/partners')">{{ copy.partners }}</NuxtLink>
        <span aria-hidden="true">/</span><span aria-current="page">{{ copy.title }}</span>
      </nav>

      <header class="affiliate-hero">
        <div class="affiliate-hero__copy">
          <p class="affiliate-kicker affiliate-kicker--hero"><span aria-hidden="true" />{{ copy.eyebrow }}</p>
          <h1>{{ copy.headline }}</h1>
          <p class="affiliate-hero__lead">{{ copy.lead }}</p>
          <div class="affiliate-hero__actions">
            <button type="button" class="affiliate-button" @click="openCabinet">{{ $auth.loggedIn ? copy.openCabinet : copy.apply }} <span aria-hidden="true">↗</span></button>
            <a href="#how-it-works" class="affiliate-hero__link">{{ copy.howLink }} <span aria-hidden="true">↓</span></a>
          </div>
          <p class="affiliate-hero__hint">{{ copy.heroHint }}</p>
        </div>
        <div class="affiliate-hero__visual" aria-hidden="true">
          <div class="affiliate-hero__halo" />
          <img src="/img/affiliates/hero.png" alt="" width="1254" height="1254" fetchpriority="high">
        </div>
      </header>

      <aside class="affiliate-status" :aria-label="copy.statusTitle">
        <span class="affiliate-status__icon" aria-hidden="true">i</span>
        <div><strong>{{ copy.statusTitle }}</strong><p>{{ copy.availability }}</p></div>
      </aside>

      <section class="affiliate-audience" aria-labelledby="affiliate-audience-title">
        <div class="affiliate-section-intro">
          <p class="affiliate-kicker">{{ copy.audienceKicker }}</p>
          <h2 id="affiliate-audience-title">{{ copy.audienceTitle }}</h2>
          <p>{{ copy.audienceIntro }}</p>
        </div>
        <div class="affiliate-audience__list">
          <article v-for="(item, index) in copy.audiences" :key="index" class="affiliate-audience__item">
            <span class="affiliate-audience__number">0{{ index + 1 }}</span>
            <div><h3>{{ item.title }}</h3><p>{{ item.description }}</p></div>
          </article>
        </div>
      </section>

      <section class="affiliate-placement" aria-labelledby="affiliate-placement-title">
        <div class="affiliate-section-intro">
          <p class="affiliate-kicker">{{ copy.placementKicker }}</p>
          <h2 id="affiliate-placement-title">{{ copy.placementTitle }}</h2>
          <p>{{ copy.placementIntro }}</p>
        </div>
        <div class="affiliate-placement__grid">
          <article v-for="(item, index) in copy.placements" :key="index" class="affiliate-placement__card">
            <span class="affiliate-placement__icon" aria-hidden="true">
              <svg v-if="index === 0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="m3 6.5 6-2.5 6 2.5L21 4v13.5L15 20l-6-2.5L3 20V6.5Z" />
                <path d="M9 4v13.5M15 6.5V20" />
              </svg>
              <svg v-else-if="index === 1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="5" width="18" height="14" rx="3" />
                <path d="m10 9 5 3-5 3V9Z" />
              </svg>
              <svg v-else viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="7" width="18" height="13" rx="2" />
                <path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M3 12h18M10 12v2h4v-2" />
              </svg>
            </span>
            <h3>{{ item.title }}</h3>
            <p>{{ item.description }}</p>
          </article>
        </div>
        <p class="affiliate-placement__note">{{ copy.placementNote }}</p>
      </section>

      <section id="how-it-works" class="affiliate-process" aria-labelledby="affiliate-process-title">
        <div class="affiliate-section-intro">
          <p class="affiliate-kicker">{{ copy.processKicker }}</p>
          <h2 id="affiliate-process-title">{{ copy.howTitle }}</h2>
          <p>{{ copy.processIntro }}</p>
        </div>
        <ol class="affiliate-process__steps">
          <li v-for="(step, index) in copy.steps" :key="index">
            <span class="affiliate-process__number">0{{ index + 1 }}</span>
            <div><h3>{{ step.title }}</h3><p>{{ step.description }}</p></div>
          </li>
        </ol>
      </section>

      <section class="affiliate-details" aria-labelledby="affiliate-details-title">
        <div class="affiliate-section-intro">
          <p class="affiliate-kicker">{{ copy.detailsKicker }}</p>
          <h2 id="affiliate-details-title">{{ copy.detailsTitle }}</h2>
          <p>{{ copy.detailsIntro }}</p>
        </div>
        <div class="affiliate-details__grid">
          <article v-for="(item, index) in copy.details" :key="index">
            <span class="affiliate-details__number">0{{ index + 1 }}</span>
            <h3>{{ item.title }}</h3>
            <p>{{ item.description }}</p>
          </article>
        </div>
      </section>

      <section class="affiliate-faq" aria-labelledby="affiliate-faq-title">
        <div class="affiliate-section-intro">
          <p class="affiliate-kicker">{{ copy.faqKicker }}</p>
          <h2 id="affiliate-faq-title">{{ copy.faqTitle }}</h2>
        </div>
        <div class="affiliate-faq__list">
          <details v-for="(item, index) in copy.faq" :key="index">
            <summary>{{ item.question }}<span aria-hidden="true">+</span></summary>
            <p>{{ item.answer }}</p>
          </details>
        </div>
      </section>

      <section class="affiliate-close" :aria-label="copy.closeTitle">
        <div><p class="affiliate-kicker">{{ copy.closeKicker }}</p><h2>{{ copy.closeTitle }}</h2><p>{{ copy.closeText }}</p></div>
        <button type="button" class="affiliate-button" @click="openCabinet">{{ $auth.loggedIn ? copy.openCabinet : copy.apply }} <span aria-hidden="true">↗</span></button>
      </section>

      <p class="affiliate-rental"><span>{{ copy.differenceTitle }} {{ copy.differenceText }}</span><NuxtLink :to="localePath('/partners')">{{ copy.rentalLink }}</NuxtLink></p>
    </div>
  </div>
</template>

<script>
import noindexMeta from '~/mixins/noindex'

const copyByLocale = {
  ru: {
    breadcrumb: 'Навигация', partners: 'Партнёрам', title: 'Партнёрская программа для аффилиатов',
    eyebrow: 'CheapCarRent для авторов и площадок',
    headline: 'Помогайте путешественникам находить автомобиль для поездки',
    lead: 'Ведёте сайт, блог или сообщество о путешествиях? Расскажите о своей площадке и подайте заявку на сотрудничество с CheapCarRent.',
    apply: 'Подать заявку', openCabinet: 'Открыть кабинет', howLink: 'Как это работает',
    heroHint: 'Заявка займёт несколько минут. Её рассмотрит команда CheapCarRent.',
    statusTitle: 'Приём заявок открыт. Программа готовится к запуску.',
    availability: 'Партнёрские ссылки, статистика и выплаты пока недоступны. Отправка заявки не означает одобрение; условия сотрудничества будут опубликованы после утверждения.',
    audienceKicker: 'Кому подойдёт', audienceTitle: 'Для тех, кто помогает людям планировать поездки',
    audienceIntro: 'Расскажите нам о своей аудитории и о том, где она ищет идеи для путешествий.',
    audiences: [
      { title: 'Сайты и путеводители', description: 'Публикации о направлениях, маршрутах и практических деталях поездки.' },
      { title: 'Блоги и авторы', description: 'Личный опыт, подборки и советы для читателей и зрителей.' },
      { title: 'Туристические сервисы', description: 'Площадки, которые помогают планировать дорогу, проживание и впечатления.' },
    ],
    placementKicker: 'Сценарии сотрудничества', placementTitle: 'Где рекомендация будет полезна',
    placementIntro: 'Расскажите об аренде автомобиля там, где читатель уже выбирает маршрут или планирует поездку.',
    placements: [
      { title: 'Путеводитель по направлению', description: 'Добавьте аренду автомобиля в материал о городе, регионе или самостоятельном маршруте.' },
      { title: 'Блог или видео', description: 'Покажите, как автомобиль вписывается в ваш личный план поездки и кому он пригодится.' },
      { title: 'Туристический сервис', description: 'Предложите посетителю рассмотреть автомобиль при планировании транспорта и проживания.' },
    ],
    placementNote: 'Это примеры форматов. Персональные ссылки и инструменты размещения будут доступны только после запуска программы и одобрения заявки.',
    processKicker: 'Простой старт', howTitle: 'Как присоединиться',
    processIntro: 'Сейчас мы знакомимся с будущими партнёрами и собираем заявки.',
    steps: [
      { title: 'Создайте аккаунт', description: 'Зарегистрируйтесь в CheapCarRent или войдите в существующий аккаунт.' },
      { title: 'Расскажите о площадке', description: 'В кабинете укажите сайт или публичную страницу и способ продвижения.' },
      { title: 'Дождитесь решения', description: 'Мы рассмотрим заявку. Инструменты появятся после запуска программы.' },
    ],
    detailsKicker: 'Перед заявкой', detailsTitle: 'Что важно знать заранее',
    detailsIntro: 'Мы открыли приём заявок, чтобы понять площадки и способы продвижения до запуска технических инструментов.',
    details: [
      { title: 'Что указать', description: 'Название площадки, ссылку на сайт или публичную страницу и описание того, как вы планируете рассказывать об аренде.' },
      { title: 'Что есть в кабинете', description: 'После отправки можно увидеть заявку и её статус. Генерация ссылок, переходы, бронирования и начисления пока не подключены.' },
      { title: 'Что будет согласовано отдельно', description: 'Ставка, правила учёта заказов, сроки и способы выплат будут определены и опубликованы до начала работы программы.' },
    ],
    faqKicker: 'Вопросы и ответы', faqTitle: 'Частые вопросы',
    faq: [
      { question: 'Можно ли уже получать комиссию за бронирования?', answer: 'Нет. Сейчас принимаются только заявки. Партнёрские ссылки, учёт привлечённых заказов и выплаты ещё не запущены.' },
      { question: 'Какой будет ставка и когда выплачивается вознаграждение?', answer: 'Коммерческие условия ещё не утверждены. Размер вознаграждения, основания для начисления, сроки и способы выплат будут опубликованы до запуска программы.' },
      { question: 'Как отслеживать переходы и бронирования?', answer: 'Сейчас кабинет показывает заявку и её статус. Статистика переходов и бронирований появится только после внедрения партнёрского трекинга.' },
      { question: 'Нужен ли собственный сайт?', answer: 'Укажите сайт или доступную для просмотра публичную страницу. В заявке опишите аудиторию и способ продвижения; команда оценит площадку.' },
      { question: 'Можно ли размещать свои автомобили через эту программу?', answer: 'Для прокатных компаний есть отдельная страница «Партнёрам». Эта программа предназначена для площадок, которые приводят клиентов.' },
    ],
    closeKicker: 'Начните знакомство', closeTitle: 'Расскажите о своей площадке',
    closeText: 'Подайте заявку сейчас и следите за её статусом в кабинете аффилиата.',
    differenceTitle: 'У вас прокатная компания?', differenceText: 'Для размещения собственного автопарка есть отдельный раздел.',
    rentalLink: 'Перейти к размещению автомобилей →',
  },
  en: {
    breadcrumb: 'Navigation', partners: 'Partners', title: 'Affiliate program',
    eyebrow: 'CheapCarRent for creators and publishers',
    headline: 'Help travellers find the right car for their journey',
    lead: 'Run a travel website, blog or community? Tell us about your audience and apply to partner with CheapCarRent.',
    apply: 'Apply to join', openCabinet: 'Open dashboard', howLink: 'How it works',
    heroHint: 'Applying takes just a few minutes. The CheapCarRent team will review your application.',
    statusTitle: 'Applications are open. The program is being prepared.',
    availability: 'Affiliate links, statistics and payouts are not available yet. An application does not guarantee approval; terms will be published once finalized.',
    audienceKicker: 'Who it is for', audienceTitle: 'For people who help others plan their trips',
    audienceIntro: 'Tell us about your audience and where they look for travel ideas.',
    audiences: [
      { title: 'Websites and guides', description: 'Articles about destinations, routes and practical travel details.' },
      { title: 'Blogs and creators', description: 'Firsthand stories, recommendations and tips for readers and viewers.' },
      { title: 'Travel services', description: 'Platforms helping people plan transport, stays and experiences.' },
    ],
    placementKicker: 'Ways to collaborate', placementTitle: 'Where a recommendation fits',
    placementIntro: 'Introduce car rental when your audience is already choosing a route or planning a trip.',
    placements: [
      { title: 'Destination guide', description: 'Include car rental in a guide to a city, region or self-drive itinerary.' },
      { title: 'Blog or video', description: 'Show how a car fits into your own travel plan and who might find it useful.' },
      { title: 'Travel service', description: 'Give visitors a car rental option while they plan transport and accommodation.' },
    ],
    placementNote: 'These are examples. Personal links and placement tools will only become available after the program launches and an application is approved.',
    processKicker: 'Getting started', howTitle: 'How to join',
    processIntro: 'We are getting to know future partners and collecting applications.',
    steps: [
      { title: 'Create an account', description: 'Sign up for CheapCarRent or use your existing account.' },
      { title: 'Tell us about your platform', description: 'Add your website or public page and explain how you plan to promote rentals.' },
      { title: 'Wait for a decision', description: 'We will review your application. Tools will become available after launch.' },
    ],
    detailsKicker: 'Before you apply', detailsTitle: 'What to know before joining',
    detailsIntro: 'We are collecting applications to learn about potential partners and their promotion plans before the tools launch.',
    details: [
      { title: 'What to provide', description: 'Your platform name, a website or public page, and a short description of how you plan to introduce car rental to your audience.' },
      { title: 'What the dashboard does', description: 'Once submitted, you can see your application and its status. Link generation, clicks, bookings and commissions are not connected yet.' },
      { title: 'What will be agreed later', description: 'Rates, booking attribution rules, payout timing and methods will be defined and published before the program starts.' },
    ],
    faqKicker: 'Questions and answers', faqTitle: 'Frequently asked questions',
    faq: [
      { question: 'Can I already earn commission on bookings?', answer: 'No. We are only accepting applications at this stage. Affiliate links, booking attribution and payouts have not launched.' },
      { question: 'What will the rate be, and when will I be paid?', answer: 'Commercial terms have not been finalized. Commission rates, eligibility, payment timing and methods will be published before launch.' },
      { question: 'How can I track clicks and bookings?', answer: 'The dashboard currently shows your application and its status. Click and booking statistics will become available after affiliate tracking is implemented.' },
      { question: 'Do I need my own website?', answer: 'Provide a website or publicly accessible page. Describe your audience and promotion plan in the application so the team can review your platform.' },
      { question: 'Can I list my own cars through this program?', answer: 'Rental companies have a separate Partners page. This program is for publishers and platforms that refer customers.' },
    ],
    closeKicker: 'Get acquainted', closeTitle: 'Tell us about your platform',
    closeText: 'Apply now and follow the status of your application in the affiliate dashboard.',
    differenceTitle: 'Are you a rental company?', differenceText: 'There is a separate path for listing your own fleet.',
    rentalLink: 'List your vehicles →',
  },
}

export default defineNuxtComponent({
  mixins: [noindexMeta],
  setup() {
    const { locale } = useI18n()
    useHead(() => ({ title: (copyByLocale[locale.value] || copyByLocale.en).title,
      meta: [{ key: 'robots', name: 'robots', content: 'noindex, follow' }] }))
  },
  computed: {
    copy() {
      const locale = this.$i18n?.locale?.value || this.$i18n?.locale || 'en'
      return copyByLocale[locale] || copyByLocale.en
    },
  },
  methods: { openCabinet() {
    if (this.$auth.loggedIn) { this.$router.push(this.localePath('/profile/affiliate-cabinet')); return }
    this.$router.push(this.localePath('/partners/affiliate-login'))
  } },
})
</script>

<style scoped>
.affiliate-page { color: #102a4c; background: #fff; }
.affiliate-wrap { width: min(1200px, calc(100% - 48px)); margin: 0 auto; }
.affiliate-crumbs { display: flex; flex-wrap: wrap; gap: 9px; align-items: center; padding-top: 26px; color: #61758a; font-size: 14px; }
.affiliate-crumbs a, .affiliate-rental a { color: #116cb1; text-decoration: underline; text-underline-offset: 3px; }
.affiliate-hero { display: grid; grid-template-columns: minmax(0, 1.04fr) minmax(0, .96fr); gap: clamp(20px, 4vw, 70px); align-items: center; min-height: 570px; padding: 34px 0 56px; }
.affiliate-hero__copy { position: relative; z-index: 1; min-width: 0; }
.affiliate-kicker { margin: 0; color: #137ac9; font-size: 13px; font-weight: 800; letter-spacing: .12em; text-transform: uppercase; }
.affiliate-kicker--hero { display: flex; gap: 10px; align-items: center; }
.affiliate-kicker--hero span { width: 9px; height: 9px; border-radius: 50%; background: #ffc543; box-shadow: 0 0 0 5px #fff4d6; }
.affiliate-hero h1 { max-width: 680px; margin: 20px 0 0; color: #0a2540; font-size: clamp(38px, 4.2vw, 60px); font-weight: 800; line-height: 1.09; letter-spacing: -.035em; text-wrap: balance; }
.affiliate-hero__lead { max-width: 580px; margin: 25px 0 0; color: #49627a; font-size: clamp(17px, 1.5vw, 20px); line-height: 1.6; }
.affiliate-hero__actions { display: flex; flex-wrap: wrap; align-items: center; gap: 18px 28px; margin-top: 34px; }
.affiliate-button { display: inline-flex; align-items: center; justify-content: center; gap: 18px; min-height: 56px; padding: 0 24px; border: 0; border-radius: 12px; background: #137ac9; color: #fff; font-size: 16px; font-weight: 800; cursor: pointer; box-shadow: 0 10px 24px #137ac92b; transition: background 150ms ease, transform 150ms ease; }
.affiliate-button:hover { background: #075f9f; transform: translateY(-2px); }
.affiliate-button span { font-size: 21px; font-weight: 400; line-height: 1; }
.affiliate-hero__link { color: #0e5f9c; font-size: 16px; font-weight: 700; text-decoration: none; border-bottom: 1px solid #9fc5df; padding-bottom: 3px; }
.affiliate-hero__link span { margin-left: 7px; }
.affiliate-hero__hint { margin: 15px 0 0; color: #64798d; font-size: 14px; line-height: 1.5; }
.affiliate-hero__visual { position: relative; display: grid; place-items: center; min-width: 0; min-height: 500px; isolation: isolate; }
.affiliate-hero__halo { position: absolute; z-index: -1; width: min(94%, 520px); aspect-ratio: 1; border-radius: 46% 54% 58% 42%; background: radial-gradient(circle at 30% 30%, #eff8ff, #d8eafa 68%, #f7fbff 69%); transform: rotate(-8deg); }
.affiliate-hero__visual img { display: block; width: min(115%, 610px); max-width: none; height: auto; filter: drop-shadow(0 24px 20px #123b6329); }
.affiliate-status { display: flex; gap: 18px; align-items: flex-start; padding: 22px 26px; border: 1px solid #d9e6f1; border-left: 5px solid #ffc543; border-radius: 14px; background: #f6faff; }
.affiliate-status__icon { flex: none; display: grid; place-items: center; width: 30px; height: 30px; border-radius: 50%; background: #dcedf9; color: #0b6cae; font-size: 17px; font-weight: 800; }
.affiliate-status strong { display: block; margin: 0 0 5px; font-size: 17px; }
.affiliate-status p { margin: 0; color: #4f6478; font-size: 15px; line-height: 1.55; }
.affiliate-audience, .affiliate-process { display: grid; grid-template-columns: minmax(0, .9fr) minmax(0, 1.1fr); gap: clamp(40px, 7vw, 100px); }
.affiliate-audience { padding: 105px 0 110px; }
.affiliate-section-intro h2, .affiliate-close h2 { margin: 16px 0 0; color: #0a2540; font-size: clamp(31px, 3.2vw, 43px); line-height: 1.18; letter-spacing: -.025em; text-wrap: balance; }
.affiliate-section-intro > p:last-child { max-width: 430px; margin: 20px 0 0; color: #63788c; font-size: 17px; line-height: 1.6; }
.affiliate-audience__list { border-top: 1px solid #d9e4ed; }
.affiliate-audience__item { display: grid; grid-template-columns: 45px 1fr; gap: 15px; padding: 23px 0; border-bottom: 1px solid #d9e4ed; }
.affiliate-audience__number { padding-top: 4px; color: #137ac9; font-size: 16px; font-weight: 800; }
.affiliate-audience__item h3, .affiliate-process__steps h3 { margin: 0; color: #102a4c; font-size: 20px; line-height: 1.3; }
.affiliate-audience__item p, .affiliate-process__steps p { margin: 7px 0 0; color: #5d7084; font-size: 16px; line-height: 1.5; }
.affiliate-placement { padding: 0 0 105px; }
.affiliate-placement .affiliate-section-intro > p:last-child { max-width: 690px; }
.affiliate-placement__grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 18px; margin-top: 34px; }
.affiliate-placement__card { min-width: 0; padding: 30px; border: 1px solid #d9e6f1; border-radius: 19px; background: #fff; box-shadow: 0 12px 36px #0a254008; }
.affiliate-placement__icon { display: grid; place-items: center; width: 46px; height: 46px; border-radius: 13px; background: #e8f5ff; color: #137ac9; font-size: 25px; font-weight: 700; }
.affiliate-placement__icon svg { display: block; width: 25px; height: 25px; }
.affiliate-placement__card h3, .affiliate-details__grid h3 { margin: 23px 0 9px; color: #102a4c; font-size: 20px; line-height: 1.3; }
.affiliate-placement__card p, .affiliate-details__grid p { margin: 0; color: #5d7084; font-size: 16px; line-height: 1.55; }
.affiliate-placement__note { max-width: 850px; margin: 24px 0 0; color: #64798d; font-size: 14px; line-height: 1.55; }
.affiliate-process { padding: 64px clamp(28px, 5vw, 70px); border-radius: 24px; background: #eff6fb; scroll-margin-top: 25px; }
.affiliate-process__steps { display: grid; gap: 0; margin: 0; padding: 0; list-style: none; }
.affiliate-process__steps li { display: grid; grid-template-columns: 54px 1fr; gap: 18px; padding: 0 0 27px; position: relative; }
.affiliate-process__steps li:not(:last-child)::before { content: ''; position: absolute; left: 23px; top: 47px; bottom: 4px; width: 2px; background: #c7dce9; }
.affiliate-process__steps li:last-child { padding-bottom: 0; }
.affiliate-process__number { display: grid; place-items: center; width: 48px; height: 48px; border-radius: 50%; background: #fff; color: #137ac9; font-size: 16px; font-weight: 800; box-shadow: 0 2px 10px #0a254012; }
.affiliate-process__steps h3 { padding-top: 2px; }
.affiliate-details { padding: 105px 0 95px; }
.affiliate-details .affiliate-section-intro > p:last-child { max-width: 700px; }
.affiliate-details__grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 28px; margin-top: 38px; }
.affiliate-details__grid article { min-width: 0; padding-top: 23px; border-top: 2px solid #d9e6f1; }
.affiliate-details__number { color: #137ac9; font-size: 15px; font-weight: 800; }
.affiliate-details__grid h3 { margin-top: 17px; }
.affiliate-faq { display: grid; grid-template-columns: minmax(0, .75fr) minmax(0, 1.25fr); gap: clamp(35px, 6vw, 95px); padding: 58px clamp(25px, 5vw, 70px); border-radius: 24px; background: #f6faff; }
.affiliate-faq__list { border-top: 1px solid #d9e6f1; }
.affiliate-faq__list details { border-bottom: 1px solid #d9e6f1; }
.affiliate-faq__list summary { display: flex; align-items: center; justify-content: space-between; gap: 18px; padding: 22px 0; color: #102a4c; font-size: 17px; font-weight: 750; line-height: 1.4; cursor: pointer; list-style: none; }
.affiliate-faq__list summary::-webkit-details-marker { display: none; }
.affiliate-faq__list summary span { flex: none; color: #137ac9; font-size: 25px; font-weight: 400; line-height: 1; transition: transform 150ms ease; }
.affiliate-faq__list details[open] summary span { transform: rotate(45deg); }
.affiliate-faq__list details p { margin: -7px 32px 22px 0; color: #5d7084; font-size: 16px; line-height: 1.6; }
.affiliate-close { display: flex; align-items: center; justify-content: space-between; gap: 40px; margin: 100px 0 38px; padding: 44px clamp(28px, 5vw, 64px); border-radius: 22px; background: #0a2540; color: #fff; }
.affiliate-close .affiliate-kicker { color: #9fd0f7; }
.affiliate-close h2 { margin-top: 12px; color: #fff; }
.affiliate-close p:last-child { margin: 12px 0 0; color: #d2e2ee; font-size: 16px; line-height: 1.5; }
.affiliate-close .affiliate-button { flex: none; background: #ffc543; color: #0a2540; box-shadow: none; }
.affiliate-close .affiliate-button:hover { background: #ffdc79; }
.affiliate-rental { display: flex; gap: 12px; align-items: center; justify-content: center; flex-wrap: wrap; margin: 0; padding: 0 0 80px; color: #5d7084; font-size: 15px; text-align: center; }
.affiliate-rental a { font-weight: 700; }
.affiliate-page a:focus-visible, .affiliate-page button:focus-visible { outline: 3px solid #ffc543; outline-offset: 4px; }
/* Compact type scale: headings are about 30% smaller; reading text stays legible. */
.affiliate-crumbs, .affiliate-hero__hint, .affiliate-placement__note { font-size: 12px; }
.affiliate-kicker { font-size: 11px; }
.affiliate-hero h1 { font-size: clamp(27px, 3vw, 42px); }
.affiliate-hero__lead { font-size: clamp(14px, 1.1vw, 16px); }
.affiliate-button, .affiliate-hero__link { font-size: 14px; }
.affiliate-status strong, .affiliate-status p { font-size: 14px; }
.affiliate-section-intro h2, .affiliate-close h2 { font-size: clamp(22px, 2.3vw, 30px); }
.affiliate-section-intro > p:last-child { font-size: 14px; }
.affiliate-audience__number, .affiliate-details__number { font-size: 13px; }
.affiliate-audience__item h3, .affiliate-process__steps h3, .affiliate-placement__card h3, .affiliate-details__grid h3 { font-size: 16px; }
.affiliate-audience__item p, .affiliate-process__steps p, .affiliate-placement__card p, .affiliate-details__grid p, .affiliate-faq__list details p, .affiliate-close p:last-child { font-size: 14px; }
.affiliate-process__number { font-size: 14px; }
.affiliate-faq__list summary { font-size: 14px; }
.affiliate-rental { font-size: 13px; }
/* The closing application prompt sits lightly on the white page. */
.affiliate-close { margin: 72px 0 28px; padding: 30px 0; border-top: 1px solid #d9e6f1; border-radius: 0; background: transparent; color: #102a4c; }
.affiliate-close .affiliate-kicker { color: #137ac9; }
.affiliate-close h2 { margin-top: 9px; color: #0a2540; font-size: 26px; }
.affiliate-close p:last-child { margin-top: 8px; color: #5d7084; }
.affiliate-close .affiliate-button { min-height: 48px; padding: 0 20px; background: #137ac9; color: #fff; box-shadow: 0 10px 24px #137ac92b; }
.affiliate-close .affiliate-button:hover { background: #075f9f; }
@media (max-width: 850px) {
  .affiliate-hero { grid-template-columns: 1fr; gap: 0; padding-top: 58px; }
  .affiliate-hero__visual { min-height: 330px; max-height: 440px; }
  .affiliate-hero__visual img { width: min(100%, 500px); }
  .affiliate-hero__halo { width: min(80%, 420px); }
  .affiliate-audience, .affiliate-process, .affiliate-faq { grid-template-columns: 1fr; gap: 38px; }
  .affiliate-audience { padding: 78px 0; }
  .affiliate-placement__grid, .affiliate-details__grid { grid-template-columns: 1fr; }
  .affiliate-placement__grid { gap: 14px; }
  .affiliate-details__grid { gap: 25px; }
}
@media (max-width: 600px) {
  .affiliate-wrap { width: min(100% - 36px, 1200px); }
  .affiliate-crumbs { padding-top: 20px; margin-bottom: 7px; font-size: 13px; }
  .affiliate-hero { padding: 35px 0 24px; }
  .affiliate-hero h1 { margin-top: 16px; font-size: clamp(34px, 9vw, 43px); }
  .affiliate-hero__lead { margin-top: 20px; font-size: 17px; }
  .affiliate-hero__actions { gap: 18px; margin-top: 25px; }
  .affiliate-button { min-height: 52px; }
  .affiliate-hero__visual { min-height: 275px; max-height: 330px; }
  .affiliate-hero__visual img { width: min(112%, 390px); }
  .affiliate-status { gap: 12px; padding: 18px; }
  .affiliate-status strong { font-size: 15px; }
  .affiliate-status p { font-size: 14px; }
  .affiliate-audience { padding: 75px 0; }
  .affiliate-placement { padding-bottom: 75px; }
  .affiliate-placement__card { padding: 23px; }
  .affiliate-process { padding: 34px 22px; }
  .affiliate-details { padding: 75px 0; }
  .affiliate-faq { padding: 34px 22px; }
  .affiliate-faq__list summary { font-size: 16px; }
  .affiliate-close { display: grid; margin-top: 70px; padding: 34px 24px; }
  .affiliate-close .affiliate-button { justify-self: start; }
  .affiliate-rental { padding-bottom: 60px; }
  .affiliate-hero h1 { font-size: clamp(24px, 6.3vw, 30px); }
  .affiliate-hero__lead { font-size: 15px; }
  .affiliate-status strong, .affiliate-status p, .affiliate-faq__list summary { font-size: 14px; }
  .affiliate-close { margin-top: 52px; padding: 28px 0; }
  .affiliate-close h2 { font-size: 23px; }
}
</style>
