import { mount, flushPromises } from '@vue/test-utils'
import { describe, it, expect, vi, beforeEach } from 'vitest'
import Faq from '@/Pages/Faq/Faq.vue' 

// Mock axios
vi.mock('axios', () => ({
  default: {
    get: vi.fn((url) => {
      if (url === '/api/faqs/list') {
        return Promise.resolve({
          data: [
            { question: 'Comment créer un compte ?', answer: 'Cliquez sur inscription.' },
            { question: 'Comment contacter le support ?', answer: 'Envoyez un email.' },
          ],
        })
      }
      if (url === '/api/tutorials/public') {
        return Promise.resolve({
          data: [
            { title: 'Démarrer avec la plateforme', video_url: 'https://example.com/video.mp4' }
          ]
        })
      }
    }),
  }
}))

describe('Faq.vue', () => {
  let wrapper

  beforeEach(async () => {
    wrapper = mount(Faq)
    await flushPromises() // attend les appels axios
  })

  it('affiche les FAQs après chargement', () => {
    const questions = wrapper.findAll('.faq-question span:first-child')
    expect(questions).toHaveLength(2)
    expect(questions[0].text()).toContain('Comment créer un compte')
  })

 

  it('affiche la réponse après clic sur une question', async () => {
    const item = wrapper.findAll('.faq-item')[0]
    await item.trigger('click')

    expect(wrapper.find('.faq-answer').text()).toContain('Cliquez sur inscription')
  })

  it('affiche les tutoriels vidéo', () => {
    const source = wrapper.find('video source')
    expect(source.exists()).toBe(true)
    expect(source.attributes('src')).toContain('video.mp4')
  })
 })