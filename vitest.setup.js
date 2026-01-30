import { config } from '@vue/test-utils'
import { vi } from 'vitest'

// Enhanced TextEncoder that ensures compatibility with jsdom's Uint8Array
class ESBuildAndJSDOMCompatibleTextEncoder {

  encode(input) {
    if (typeof input !== "string") {
      throw new TypeError("`input` must be a string");
    }
    const decodedURI = decodeURIComponent(encodeURIComponent(input));
    const arr = new Uint8Array(decodedURI.length);
    const chars = decodedURI.split("");
    // Use for-of loop instead of traditional for loop
    for (const [index, char] of chars.entries()) {
      arr[index] = char.charCodeAt(0);
    }
    return arr;
  }
}

// Enhanced TextDecoder for compatibility
class ESBuildAndJSDOMCompatibleTextDecoder {
  constructor(encoding = 'utf-8') {
    this.encoding = encoding;
  }
  
  decode(input) {
    if (!input) return '';
    const bytes = new Uint8Array(input);
    let result = '';
    // Use for-of loop and avoid deprecated escape() function
    for (const byte of bytes) {
      result += String.fromCharCode(byte);
    }
    // Replace deprecated escape() with manual percent encoding
    return decodeURIComponent(result.replace(/[\x80-\xFF]/g, (match) => {
      return '%' + match.charCodeAt(0).toString(16).padStart(2, '0').toUpperCase();
    }));
  }
}

// Set global TextEncoder and TextDecoder
global.TextEncoder = ESBuildAndJSDOMCompatibleTextEncoder;
global.TextDecoder = ESBuildAndJSDOMCompatibleTextDecoder;

// Create the images directory mock
const mockImagePath = (imageName) => `/mocked/${imageName}`

// Mock specific images used in components
vi.mock('/images/support-person.png', () => ({
  default: mockImagePath('support-person.png')
}))

vi.mock('/images/faq-illustration.png', () => ({
  default: mockImagePath('faq-illustration.png')
}))

// Additional fallback mocks
vi.mock('@/assets/images/support-person.png', () => ({
  default: mockImagePath('support-person.png')
}))

vi.mock('@/assets/images/faq-illustration.png', () => ({
  default: mockImagePath('faq-illustration.png')
}))

config.global.stubs = {
  'router-link': true,
  'router-view': true,
}

// Global mock for any image import
config.global.mocks = {
  $mockImage: mockImagePath
}