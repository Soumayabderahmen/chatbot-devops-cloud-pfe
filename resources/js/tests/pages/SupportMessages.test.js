import { mount } from '@vue/test-utils'
import SupportMessages from '@/Pages/Admin/SupportMessages.vue'
import { describe, it, expect, vi, beforeEach } from 'vitest'
import axios from 'axios'

vi.mock('axios')

const mockMessages = {
  data: [
    { id: 1, name: 'Alice', email: 'alice@example.com', message: 'Hello', status: 'new', category: 'Tech', created_at: '2024-01-01T12:00:00' },
    { id: 2, name: 'Bob', email: 'bob@example.com', message: 'Need help', status: 'read', category: 'Billing', created_at: '2024-01-02T12:00:00' }
  ]
}

describe('SupportMessages.vue', () => {
  let wrapper

  beforeEach(() => {
    vi.clearAllMocks()
    wrapper = mount(SupportMessages, {
      props: { messages: mockMessages }
    })
  })

  it('renders support messages list', () => {
    expect(wrapper.text()).toContain('Messages de support')
    expect(wrapper.text()).toContain('Alice')
    expect(wrapper.text()).toContain('Bob')
  })

it('filters messages by status', async () => {
  wrapper.vm.filterStatus = 'new' // modification directe sans setValue
  await wrapper.vm.$nextTick()

  expect(wrapper.text()).toContain('Alice')
  expect(wrapper.text()).not.toContain('Bob')
})



  it('selects a message on click and marks as read automatically if new', async () => {
    axios.put.mockResolvedValue({})

    const msgEl = wrapper.findAll('[class*=cursor-pointer]')[0]
    await msgEl.trigger('click')

    expect(wrapper.vm.selectedMessage.name).toBe('Alice')
    expect(axios.put).toHaveBeenCalledWith('/api/admin/support-messages/1/status', { status: 'read' })
  })

  it('marks a message as replied', async () => {
    axios.put.mockResolvedValue({})

    wrapper.vm.selectedMessage = mockMessages.data[1]
    await wrapper.vm.markAs('replied', false)

    expect(wrapper.vm.selectedMessage.status).toBe('replied')
    expect(wrapper.vm.notification.message).toContain('Message marqué comme')
  })

  it('deletes a selected message', async () => {
    axios.delete.mockResolvedValue({})

    wrapper.vm.selectedMessage = mockMessages.data[0]
    await wrapper.vm.deleteMessage()

    expect(wrapper.vm.localMessages.find(m => m.id === 1)).toBeUndefined()
    expect(wrapper.vm.notification.message).toContain('supprimé avec succès')
  })

  it('shows notification message and auto-hides it', async () => {
    wrapper.vm.showNotification('Test Message', 'success')
    expect(wrapper.vm.notification.show).toBe(true)
    expect(wrapper.vm.notification.message).toBe('Test Message')

    await new Promise(resolve => setTimeout(resolve, 3100))
    expect(wrapper.vm.notification.show).toBe(false)
  })
})
