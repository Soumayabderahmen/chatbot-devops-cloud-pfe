import { mount } from '@vue/test-utils'
import { vi } from 'vitest'
import ContactUs from '@/Pages/ContactUs/ContactUs.vue'

// Mock axios and make it globally available
const mockPost = vi.fn()
const mockAxios = {
  post: mockPost
}

// Make axios available globally like it would be in a real app
global.axios = mockAxios

vi.mock('axios', () => ({
  default: mockAxios
}))

// Mock vue3-toastify - Define the mock object before using it
vi.mock('vue3-toastify', () => ({
  toast: {
    error: vi.fn()
  }
}))

// Mock ziggy-js
vi.mock('ziggy-js', () => ({
  route: vi.fn()
}))

// Mock Inertia Link
const MockLink = {
  name: 'Link',
  template: '<a><slot /></a>'
}

const createWrapper = (options = {}) => {
  return mount(ContactUs, {
    global: {
      components: {
        Link: MockLink
      },
      stubs: {
        Link: MockLink
      }
    },
    ...options
  })
}

describe('ContactUs.vue', () => {
  beforeEach(() => {
    vi.clearAllMocks()
    mockPost.mockResolvedValue({ data: { success: true } })
  })

  it('renders the contact form correctly', () => {
    const wrapper = createWrapper()

    expect(wrapper.text()).toContain('Contactez-nous')
    expect(wrapper.find('form').exists()).toBe(true)
    expect(wrapper.find('input[type="text"]').exists()).toBe(true)
    expect(wrapper.find('input[type="email"]').exists()).toBe(true)
    expect(wrapper.find('textarea').exists()).toBe(true)
    expect(wrapper.find('button[type="submit"]').exists()).toBe(true)
    expect(wrapper.find('select').exists()).toBe(true)
  })

  it('should update form data when user types', async () => {
    const wrapper = createWrapper()
    
    const nameInput = wrapper.find('input[type="text"]')
    await nameInput.setValue('John Doe')
    expect(wrapper.vm.form.name).toBe('John Doe')

    const emailInput = wrapper.find('input[type="email"]')
    await emailInput.setValue('john@example.com')
    expect(wrapper.vm.form.email).toBe('john@example.com')

    const textarea = wrapper.find('textarea')
    await textarea.setValue('Test message')
    expect(wrapper.vm.form.message).toBe('Test message')

    const select = wrapper.find('select')
    await select.setValue('technical')
    expect(wrapper.vm.form.category).toBe('technical')
  })

  it('should handle file upload correctly', async () => {
    const wrapper = createWrapper()
    
    const file = new File(['test content'], 'test.txt', { type: 'text/plain' })
    const fileInput = wrapper.find('input[type="file"]')
    
    // Mock the file input files property
    const mockFiles = [file]
    Object.defineProperty(fileInput.element, 'files', {
      value: mockFiles,
      writable: false,
    })
    
    await fileInput.trigger('change')
    
    expect(wrapper.vm.fileName).toBe('test.txt')
    expect(wrapper.vm.form.file).toBe(file)
  })

  it('should reject files larger than 2MB', async () => {
    const wrapper = createWrapper()
    
    // Get the toast mock from the mocked module
    const { toast } = await import('vue3-toastify')
    
    // Create a large file (3MB)
    const largeFile = new File(['x'.repeat(3 * 1024 * 1024)], 'large.txt', { 
      type: 'text/plain' 
    })
    
    const fileInput = wrapper.find('input[type="file"]')
    
    Object.defineProperty(fileInput.element, 'files', {
      value: [largeFile],
      writable: false,
    })
    
    await fileInput.trigger('change')
    
    // Wait for the next tick to ensure the handler has run
    await wrapper.vm.$nextTick()
    
    expect(toast.error).toHaveBeenCalledWith('Fichier trop grand (max 2 Mo)')
    expect(wrapper.vm.fileName).toBe('')
    expect(wrapper.vm.form.file).toBe(null)
  })

  it('should submit form with valid data', async () => {
    const wrapper = createWrapper()
    
    // Fill form data
    await wrapper.find('input[type="text"]').setValue('John Doe')
    await wrapper.find('input[type="email"]').setValue('john@example.com')
    await wrapper.find('textarea').setValue('Test message')
    await wrapper.find('select').setValue('technical')
    
    // Submit form
    await wrapper.find('form').trigger('submit.prevent')
    
    // Wait for async operations
    await wrapper.vm.$nextTick()
    await new Promise(resolve => setTimeout(resolve, 0))
    
    expect(mockPost).toHaveBeenCalledWith(
      '/api/contact/store',
      expect.any(FormData),
      { headers: { 'Content-Type': 'multipart/form-data' } }
    )
    
    // Check that form was reset
    expect(wrapper.vm.form.name).toBe('')
    expect(wrapper.vm.form.email).toBe('')
    expect(wrapper.vm.form.message).toBe('')
    expect(wrapper.vm.form.category).toBe('general')
  })

  it('should handle submission error', async () => {
    // Mock axios to reject
    mockPost.mockRejectedValueOnce(new Error('Network error'))
    
    const wrapper = createWrapper()
    
    // Get the toast mock from the mocked module
    const { toast } = await import('vue3-toastify')
    
    // Fill and submit form
    await wrapper.find('input[type="text"]').setValue('John Doe')
    await wrapper.find('input[type="email"]').setValue('john@example.com')
    await wrapper.find('textarea').setValue('Test message')
    
    await wrapper.find('form').trigger('submit.prevent')
    
    // Wait for async operations and error handling
    await wrapper.vm.$nextTick()
    await new Promise(resolve => setTimeout(resolve, 0))
    
    expect(toast.error).toHaveBeenCalledWith(
      'Une erreur est survenue. Veuillez réessayer.'
    )
  })

  it('should show success popup after successful submission', async () => {
    const wrapper = createWrapper()
    
    // Fill form
    await wrapper.find('input[type="text"]').setValue('John Doe')
    await wrapper.find('input[type="email"]').setValue('john@example.com')
    await wrapper.find('textarea').setValue('Test message')
    
    // Submit form
    await wrapper.find('form').trigger('submit.prevent')
    
    // Wait for async operations
    await wrapper.vm.$nextTick()
    await new Promise(resolve => setTimeout(resolve, 0))
    
    expect(wrapper.vm.showSuccessPopup).toBe(true)
  })

  it('should trigger animations on mount', async () => {
    vi.useFakeTimers()
    
    const wrapper = createWrapper()
    
    // Initially animations should be false
    expect(wrapper.vm.isFormVisible).toBe(false)
    expect(wrapper.vm.isInfoVisible).toBe(false)
    
    // Advance timers by 300ms
    vi.advanceTimersByTime(300)
    await wrapper.vm.$nextTick()
    
    expect(wrapper.vm.isFormVisible).toBe(true)
    
    // Advance timers by another 300ms
    vi.advanceTimersByTime(300)
    await wrapper.vm.$nextTick()
    
    expect(wrapper.vm.isInfoVisible).toBe(true)
    
    vi.useRealTimers()
  })
})