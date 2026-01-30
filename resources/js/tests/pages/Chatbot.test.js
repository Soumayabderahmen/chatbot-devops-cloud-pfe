import { mount } from '@vue/test-utils'
import { vi, describe, it, expect, beforeEach, beforeAll } from 'vitest'
import Chatbot from '@/Pages/Chatbot/Chatbot.vue'
import axios from 'axios'

// --- Mocks ---
vi.mock('axios')
vi.mock('./ChatInput.vue', () => ({ default: { template: '<div></div>' } }))
vi.mock('marked', () => ({ marked: { parse: vi.fn(text => text) } }))

// Mock localStorage
const localStorageMock = (() => {
  let store = {}
  return {
    getItem(key) { return store[key] || null },
    setItem(key, value) { store[key] = value },
    clear() { store = {} }
  }
})()
Object.defineProperty(window, 'localStorage', { value: localStorageMock })

// Données de settings par défaut
const mockBotSettings = {
  bot_name: 'TestBot',
  welcome_message: 'Bienvenue ! 🎉 Je suis là pour vous aider. Que puis-je faire pour vous aujourd\'hui ?',
  primary_color: '#2563eb'
}

// Helper wrapper
const createWrapper = (props = {}) => mount(Chatbot, { props })

// ✅ Polyfill AbortController pour éviter l'erreur en CI
beforeAll(() => {
  vi.stubGlobal('AbortController', class {
    constructor() {
      this.signal = { aborted: false }
    }
    abort() {
      this.signal.aborted = true
    }
  })
})

describe('Chatbot.vue', () => {
  beforeEach(() => {
    vi.clearAllMocks()
    localStorage.clear()

    // Force RAF immédiat pour pousser le welcome message
    global.requestAnimationFrame = (cb) => { cb(); return 0 }
    global.cancelAnimationFrame = () => {}

    // Évite des timers réels
    vi.spyOn(global, 'setInterval').mockImplementation(() => 0)
    vi.spyOn(global, 'setTimeout').mockImplementation((cb) => { cb(); return 0 })
    vi.spyOn(global, 'clearTimeout').mockImplementation(() => {})

    // import.meta.env (on reste en relatif en test)
    globalThis.import = { meta: { env: { VITE_CHATBOT_API_URL: '' } } }

    // Mock axios.get par défaut : settings + ping OK
    axios.get.mockImplementation((url) => {
      if (url === '/api/public/chatbot/settings') {
        return Promise.resolve({ data: mockBotSettings })
      }
      if (url === '/ping' || (typeof url === 'string' && url.endsWith('/ping'))) {
        return Promise.resolve({ status: 200, data: { status: 'ok' } })
      }
      return Promise.resolve({ data: {} })
    })
  })

  it('should load bot settings on mount', async () => {
    const wrapper = createWrapper()
    await wrapper.vm.$nextTick()

    expect(axios.get).toHaveBeenCalledWith(
      '/api/public/chatbot/settings',
      expect.objectContaining({ timeout: 3000 })
    )
    expect(wrapper.vm.botSettings.bot_name).toBe('TestBot')
  })

  it('should generate session id if not present', () => {
    const wrapper = createWrapper()
    wrapper.vm.getSessionId()
    const sessionId = localStorage.getItem('chatbot_session_id')
    expect(sessionId).toBeTruthy()
  })

  it('should toggle chatbot visibility', async () => {
    const wrapper = createWrapper()
    expect(wrapper.vm.isOpen).toBe(false)
    await wrapper.vm.toggleChatbot()
    expect(wrapper.vm.isOpen).toBe(true)
  })

  it('should add welcome message for guest user on open', async () => {
    const wrapper = createWrapper()
    wrapper.vm.welcomeShown = false

    await wrapper.vm.toggleChatbot()
    await wrapper.vm.$nextTick()

    expect(wrapper.vm.messages.length).toBe(1)
    expect(wrapper.vm.messages[0].text).toBe(mockBotSettings.welcome_message)
  })

  it('should handle sending a user message', async () => {
    global.fetch = vi.fn().mockResolvedValue({
      ok: true,
      body: { getReader: () => ({ read: vi.fn().mockResolvedValue({ done: true }) }) }
    })

    const wrapper = createWrapper()
    await wrapper.vm.sendMessage('Bonjour')

    expect(wrapper.vm.messages[0].text).toBe('Bonjour')
    expect(wrapper.vm.messages[0].sender).toBe('user')
    expect(global.fetch).toHaveBeenCalledWith(
      expect.any(String),
      expect.objectContaining({
        method: 'POST',
        headers: expect.any(Object),
        body: expect.stringContaining('Bonjour')
      })
    )
  })

  it('should save history when authenticated', async () => {
    const user = { id: 1 }

    axios.post.mockResolvedValue({})
    global.fetch = vi.fn().mockResolvedValue({
      ok: true,
      body: { getReader: () => ({ read: vi.fn().mockResolvedValue({ done: true }) }) }
    })

    const wrapper = createWrapper({ user })
    await wrapper.vm.sendMessage('Bonjour')

    await new Promise((r) => setTimeout(r, 0))

    expect(axios.post).toHaveBeenCalledWith(
      '/api/chatbot/history/save',
      expect.objectContaining({ userMessage: 'Bonjour' }),
      expect.any(Object)
    )
  })

  it('should handle bot status check', async () => {
    // Mocks spécifiques à ce test
    vi.clearAllMocks()
    axios.get.mockImplementation((url) => {
      if (url === '/api/public/chatbot/settings') {
        return Promise.resolve({ data: mockBotSettings })
      }
      if (url === '/ping' || (typeof url === 'string' && url.endsWith('/ping'))) {
        return Promise.resolve({ status: 200, data: { status: 'ok' } })
      }
      return Promise.reject(new Error(`Unexpected URL: ${url}`))
    })

    const wrapper = createWrapper()
    wrapper.vm.botStatus = 'offline'

    await wrapper.vm.checkBotStatus()
    await wrapper.vm.$nextTick()

    // ✅ Accepte URL relative OU absolue
    expect(axios.get).toHaveBeenCalledWith(
      expect.stringMatching(/\/ping$/),
      expect.objectContaining({
        timeout: 2000,
        signal: expect.any(Object)
      })
    )

    expect(wrapper.vm.botStatus).toBe('online')
  })

  it('should handle errors gracefully', async () => {
    global.fetch = vi.fn().mockRejectedValue(new Error('Network Error'))
    const wrapper = createWrapper()
    await wrapper.vm.sendMessage('Bonjour')

    expect(wrapper.vm.messages[1].error).toBe(true)
    expect(wrapper.vm.messages[1].text).toContain('Désolé, une erreur est survenue.')
  })
})
