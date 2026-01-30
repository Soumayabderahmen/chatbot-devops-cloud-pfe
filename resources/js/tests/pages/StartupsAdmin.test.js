import { mount } from '@vue/test-utils'
import { describe, it, expect, beforeEach } from 'vitest'
import StartupsAdmin from '@/Pages/Admin/StartupsAdmin.vue'

const sampleStartups = [
  {
    id: 1,
    name: 'Startup A',
    email: 'a@startup.com',
    statut: 'active',
    created_at: '2024-07-01T12:00:00Z',
    startup: {
      logo_startup: null,
      website_link: 'https://a.com',
      phone_number: '12345678',
      sector: { name: 'Tech' }
    }
  },
  {
    id: 2,
    name: 'Startup B',
    email: 'b@startup.com',
    statut: 'pending',
    created_at: '2024-07-02T12:00:00Z',
    startup: {
      logo_startup: 'logo-b.png',
      website_link: null,
      phone_number: null,
      sector: { name: 'Health' }
    }
  }
]

describe('StartupsAdmin.vue', () => {
  let wrapper

  beforeEach(() => {
    wrapper = mount(StartupsAdmin, {
      props: {
        startups: sampleStartups
      }
    })
  })

  it('renders the main title and stats', () => {
    expect(wrapper.text()).toContain('Gestion des Startups')
    expect(wrapper.text()).toContain('Total Startups')
    expect(wrapper.text()).toContain('Actives')
    expect(wrapper.text()).toContain('En attente')
  })

  it('displays the correct number of startups', () => {
    expect(wrapper.text()).toContain('2 résultat(s)')
    expect(wrapper.findAll('tbody tr').length).toBe(2)
  })

  it('filters by search query', async () => {
    await wrapper.find('input[type="text"]').setValue('Startup A')
    expect(wrapper.text()).toContain('Startup A')
    expect(wrapper.text()).not.toContain('Startup B')
  })

  it('formats date correctly', () => {
    const rowText = wrapper.find('tbody').text()
    expect(rowText).toMatch(/1 juil\. 2024|01 juil\. 2024/)
  })

  it('returns correct initials', () => {
    const vm = wrapper.vm
    expect(vm.getInitials('Startup A')).toBe('SA')
    expect(vm.getInitials('Test')).toBe('T')
  })

  it('returns correct status text and class', () => {
    const vm = wrapper.vm
    expect(vm.getStatusText('active')).toBe('Actif')
    expect(vm.getStatusText('pending')).toBe('En attente')
    expect(vm.getStatusText('invalid')).toBe('Inconnu')

    expect(vm.getStatusClass('active')).toContain('green')
    expect(vm.getStatusClass('pending')).toContain('yellow')
    expect(vm.getStatusClass('inactive')).toContain('red')
  })

 it('handles pagination correctly', async () => {
  const vm = wrapper.vm
  
  // Set the data properties directly on the Vue instance
  vm.itemsPerPage = 1
  await wrapper.vm.$nextTick()
  
  expect(wrapper.vm.paginatedStartups.length).toBe(1)

  vm.currentPage = 2
  await wrapper.vm.$nextTick()
  
  expect(wrapper.vm.paginatedStartups[0].name).toBe('Startup B')
})
})
