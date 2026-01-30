// resources/js/tests/Components/PrimaryButton.test.js
import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import PrimaryButton from '@/Components/PrimaryButton.vue'

describe('PrimaryButton', () => {
  it('renders button with default slot content', () => {
    const wrapper = mount(PrimaryButton, {
      slots: {
        default: 'Click me'
      }
    })
    
    expect(wrapper.text()).toContain('Click me')
    expect(wrapper.find('button').exists()).toBe(true)
  })

  it('emits click event when clicked', async () => {
    const wrapper = mount(PrimaryButton, {
      slots: {
        default: 'Click me'
      }
    })
    
    await wrapper.find('button').trigger('click')
    expect(wrapper.emitted('click')).toBeTruthy()
  })

  it('can be disabled', () => {
    const wrapper = mount(PrimaryButton, {
      props: {
        disabled: true
      },
      slots: {
        default: 'Disabled button'
      }
    })
    
    expect(wrapper.find('button').attributes('disabled')).toBeDefined()
  })
})