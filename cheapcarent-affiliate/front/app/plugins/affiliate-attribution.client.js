const STORAGE_KEY = 'cheapcar_affiliate_click_id'
const UUID = /^[0-9a-f]{8}-[0-9a-f]{4}-[1-8][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i

export default defineNuxtPlugin((nuxtApp) => {
  const capture = (route) => {
    const value = route.query?.affiliate_click_id
    if (typeof value !== 'string' || !UUID.test(value)) return
    try {
      window.sessionStorage.setItem(STORAGE_KEY, value)
    } catch {
      // A blocked browser storage does not prevent a normal booking.
    }
  }

  capture(nuxtApp.$router.currentRoute.value)
  nuxtApp.$router.afterEach(capture)
})
