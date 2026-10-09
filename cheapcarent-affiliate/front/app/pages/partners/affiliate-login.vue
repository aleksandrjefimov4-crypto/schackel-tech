<template>
  <main class="affiliate-login">
    <div class="affiliate-login__wrap">
      <NuxtLink class="affiliate-login__back" :to="localePath('/partners/affiliates')">
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m12 19-7-7 7-7M5 12h14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" /></svg>
        {{ copy.back }}
      </NuxtLink>
      <div class="affiliate-login__grid">
        <section class="affiliate-login__card" :aria-label="copy.title">
          <img class="affiliate-login__program-logo" src="/partners/affiliate-logo-realistic-b.png" :alt="copy.programLogo" width="112" height="112">
          <h1>{{ copy.title }}</h1>
          <p>{{ copy.formIntro }}</p>
          <form @submit.prevent="submit">
            <label for="affiliate-login-email">{{ copy.email }}</label>
            <div class="affiliate-login__field">
              <svg class="affiliate-login__field-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="3" stroke="currentColor" stroke-width="1.8"/><path d="m4 7 8 6 8-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
              <input id="affiliate-login-email" v-model.trim="email" type="email" autocomplete="email" required maxlength="255">
            </div>
            <label for="affiliate-login-password">{{ copy.password }}</label>
            <div class="affiliate-login__field">
              <svg class="affiliate-login__field-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="4" y="10" width="16" height="11" rx="2.5" stroke="currentColor" stroke-width="1.8"/><path d="M8 10V7a4 4 0 0 1 8 0v3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><circle cx="12" cy="15.5" r="1" fill="currentColor"/></svg>
              <input id="affiliate-login-password" v-model="password" :type="showPassword ? 'text' : 'password'" autocomplete="current-password" required>
              <button class="affiliate-login__reveal" type="button" :aria-label="showPassword ? copy.hidePassword : copy.showPassword" :aria-pressed="showPassword" @click="showPassword = !showPassword">
                <svg v-if="showPassword" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M3 3 21 21M10.6 10.7a2 2 0 0 0 2.7 2.7M8.7 5.3A10.6 10.6 0 0 1 12 4.8c4.5 0 8.2 3 9.5 7.2a10.8 10.8 0 0 1-3.1 4.7M6.2 6.2A11.1 11.1 0 0 0 2.5 12c1.3 4.2 5 7.2 9.5 7.2 1.4 0 2.7-.3 3.8-.8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <svg v-else viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M2.5 12C3.8 7.8 7.5 4.8 12 4.8s8.2 3 9.5 7.2c-1.3 4.2-5 7.2-9.5 7.2S3.8 16.2 2.5 12Z" stroke="currentColor" stroke-width="1.8"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.8"/></svg>
              </button>
            </div>
            <div class="affiliate-login__options">
              <label class="affiliate-login__remember" for="affiliate-remember-email">
                <input id="affiliate-remember-email" v-model="rememberEmail" type="checkbox" @change="updateRememberEmail">
                <span>{{ copy.rememberEmail }}</span>
              </label>
              <button class="affiliate-login__forgot" type="button" @click="openPasswordRecovery">{{ copy.forgotPassword }}</button>
            </div>
            <p v-if="error" class="affiliate-login__error" role="alert">{{ copy.error }}</p>
            <button type="submit" :disabled="submitting">{{ submitting ? copy.sending : copy.submit }} <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" /></svg></button>
          </form>
          <p class="affiliate-login__help">{{ copy.noAccount }} <button type="button" @click="openRegistration">{{ copy.register }}</button></p>
        </section>
      </div>
    </div>
  </main>
</template>

<script>
import noindexMeta from '~/mixins/noindex'

const REMEMBERED_EMAIL_KEY = 'cheapcar_affiliate_email'

const copies = {
  ru: {
    back: 'О партнёрской программе', title: 'Вход в кабинет партнёра', programLogo: 'Логотип партнёрской программы CheapCarRent',
    formIntro: 'Войдите с e-mail и паролем аккаунта CheapCarRent.',
    email: 'E-mail', password: 'Пароль', showPassword: 'Показать пароль', hidePassword: 'Скрыть пароль', rememberEmail: 'Запомнить e-mail', forgotPassword: 'Забыли пароль?', submit: 'Войти в кабинет', sending: 'Вход…',
    error: 'Не удалось войти. Проверьте e-mail и пароль.', noAccount: 'Нет аккаунта?', register: 'Создать аккаунт',
  },
  en: {
    back: 'About the affiliate program', title: 'Affiliate sign in', programLogo: 'CheapCarRent affiliate program logo',
    formIntro: 'Use your CheapCarRent account email and password.',
    email: 'Email', password: 'Password', showPassword: 'Show password', hidePassword: 'Hide password', rememberEmail: 'Remember email', forgotPassword: 'Forgot password?', submit: 'Sign in to dashboard', sending: 'Signing in…',
    error: 'Could not sign in. Check your email and password.', noAccount: 'New here?', register: 'Create an account',
  },
}

export default defineNuxtComponent({
  mixins: [noindexMeta],
  setup() {
    const { locale } = useI18n()
    useHead(() => ({
      title: `${(copies[locale.value] || copies.en).title} — CheapCarRent`,
      meta: [{ key: 'robots', name: 'robots', content: 'noindex, follow' }],
    }))
  },
  data() { return { email: '', password: '', rememberEmail: false, showPassword: false, submitting: false, error: false, waitingForRegistration: false } },
  mounted() {
    try {
      const savedEmail = window.localStorage.getItem(REMEMBERED_EMAIL_KEY)
      if (savedEmail) {
        this.email = savedEmail
        this.rememberEmail = true
      }
    } catch (_) {
      // Login remains available when browser storage is disabled.
    }
  },
  computed: {
    copy() {
      const locale = this.$i18n?.locale?.value || this.$i18n?.locale || 'en'
      return copies[locale] || copies.en
    },
  },
  watch: {
    '$auth.loggedIn'(loggedIn) {
      if (loggedIn && this.waitingForRegistration) this.$router.push(this.localePath('/profile/affiliate-cabinet'))
    },
  },
  methods: {
    async submit() {
      if (this.submitting) return
      this.submitting = true
      this.error = false
      try {
        await this.$auth.loginWith('laravelJWT', { data: { email: this.email, password: this.password, partner: false } })
        this.updateRememberEmail()
        await this.$router.push(this.localePath('/profile/affiliate-cabinet'))
      } catch (_) {
        this.error = true
      } finally {
        this.submitting = false
      }
    },
    openRegistration() {
      this.waitingForRegistration = true
      this.$store.commit('auth_popup/setStep', 'register')
      this.$store.commit('auth_popup/open')
    },
    openPasswordRecovery() {
      this.$store.commit('auth_popup/setStep', 'forgot')
      this.$store.commit('auth_popup/open')
    },
    updateRememberEmail() {
      try {
        if (this.rememberEmail && this.$auth.loggedIn) window.localStorage.setItem(REMEMBERED_EMAIL_KEY, this.email)
        else if (!this.rememberEmail) window.localStorage.removeItem(REMEMBERED_EMAIL_KEY)
      } catch (_) {
        // Storage is optional and must not block sign in.
      }
    },
  },
})
</script>

<style scoped>
.affiliate-login { min-height: calc(100vh - 160px); padding: 32px 0 92px; color: #102a4c; background: radial-gradient(circle at 9% 8%, #e7f4ff 0, transparent 32%), #f8fbfe; }
.affiliate-login__wrap { width: min(1180px, calc(100% - 40px)); margin: 0 auto; }
.affiliate-login__back { display: inline-flex; align-items: center; gap: 9px; margin-bottom: 52px; color: #137ac9; font-size: 14px; font-weight: 700; }
.affiliate-login__back svg { width: 18px; height: 18px; transition: transform .2s ease; }
.affiliate-login__back:hover svg { transform: translateX(-3px); }
.affiliate-login__grid { display: flex; justify-content: center; align-items: center; min-height: 600px; }
.affiliate-login__card { width: min(100%, 480px); padding: clamp(26px, 4vw, 44px); border: 1px solid #d9e9f5; border-radius: 28px; background: #fff; box-shadow: 0 24px 60px #14375512; }
.affiliate-login__program-logo { display: block; width: 112px; height: 112px; object-fit: contain; filter: drop-shadow(0 10px 14px rgba(19, 122, 201, .12)); }
.affiliate-login__card h1 { margin: 23px 0 7px; font-size: 27px; line-height: 1.2; }
.affiliate-login__card > p { margin: 0; color: #64788d; line-height: 1.5; }
.affiliate-login__card form { display: grid; margin-top: 28px; }
.affiliate-login__card label { margin: 0 0 8px; font-size: 14px; font-weight: 700; }
.affiliate-login__field { position: relative; margin-bottom: 19px; }
.affiliate-login__field-icon { position: absolute; top: 50%; left: 15px; width: 20px; height: 20px; color: #6689a6; transform: translateY(-50%); pointer-events: none; }
.affiliate-login__card input { width: 100%; height: 49px; padding: 0 46px; border: 1px solid #b7cfe1; border-radius: 11px; background: #fff; color: #102a4c; font: inherit; }
.affiliate-login__card input:focus { outline: 3px solid #c7e6ff; border-color: #137ac9; }
.affiliate-login__field:focus-within .affiliate-login__field-icon { color: #137ac9; }
.affiliate-login__reveal { position: absolute; top: 50%; right: 10px; display: grid; width: 34px; height: 34px; place-items: center; padding: 0; border: 0; border-radius: 7px; background: transparent; color: #6689a6; transform: translateY(-50%); cursor: pointer; }
.affiliate-login__reveal:hover { background: #eaf5fd; color: #137ac9; }
.affiliate-login__reveal:focus-visible { outline: 2px solid #137ac9; outline-offset: 2px; }
.affiliate-login__reveal svg { width: 19px; height: 19px; }
.affiliate-login__options { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; margin: -3px 0 20px; }
.affiliate-login__card .affiliate-login__remember { display: inline-flex; align-items: center; gap: 9px; margin: 0; color: #526a80; font-size: 14px; font-weight: 600; cursor: pointer; }
.affiliate-login__remember input { appearance: none; width: 19px; height: 19px; margin: 0; padding: 0; border: 1.5px solid #9ab8ce; border-radius: 5px; background: #fff; cursor: pointer; }
.affiliate-login__remember input:checked { border-color: #137ac9; background: #137ac9; box-shadow: inset 0 0 0 4px #137ac9; }
.affiliate-login__remember input:checked::after { content: ''; display: block; width: 9px; height: 5px; margin: 4px 0 0 3px; border: solid #fff; border-width: 0 0 2px 2px; transform: rotate(-45deg); }
.affiliate-login__remember input:focus-visible { outline: 3px solid #c7e6ff; outline-offset: 2px; }
.affiliate-login__forgot { padding: 2px 0; border: 0; background: none; color: #137ac9; font: inherit; font-size: 14px; font-weight: 700; cursor: pointer; }
.affiliate-login__forgot:hover { text-decoration: underline; }
.affiliate-login__forgot:focus-visible { outline: 3px solid #c7e6ff; outline-offset: 4px; border-radius: 2px; }
.affiliate-login__card form > button[type="submit"] { display: flex; justify-content: space-between; align-items: center; min-height: 51px; padding: 0 19px; border: 0; border-radius: 11px; background: #137ac9; color: #fff; font: inherit; font-weight: 800; cursor: pointer; }
.affiliate-login__card form > button[type="submit"] svg { width: 20px; height: 20px; flex: none; transition: transform .2s ease; }
.affiliate-login__card form > button[type="submit"]:hover:not(:disabled) svg { transform: translateX(3px); }
.affiliate-login__card form > button[type="submit"]:disabled { opacity: .65; cursor: wait; }
.affiliate-login__error { margin: 0 0 17px; color: #af2424; font-size: 14px; }
.affiliate-login__card .affiliate-login__help { margin-top: 25px; font-size: 14px; }
.affiliate-login__help button { padding: 0; border: 0; background: none; color: #137ac9; font: inherit; font-weight: 800; cursor: pointer; }
@media (max-width: 800px) { .affiliate-login__grid { min-height: 0; } .affiliate-login__back { margin-bottom: 34px; } }
@media (max-width: 500px) { .affiliate-login { padding: 23px 0 65px; } .affiliate-login__wrap { width: calc(100% - 32px); } .affiliate-login__card { padding: 23px; border-radius: 20px; } }
</style>
