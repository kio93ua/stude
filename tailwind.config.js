import defaultTheme from 'tailwindcss/defaultTheme'
import forms from '@tailwindcss/forms'
import typography from '@tailwindcss/typography'
import aspectRatio from '@tailwindcss/aspect-ratio'

/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './resources/views/**/*.blade.php',
    './resources/js/**/*.{js,vue}',
    './app/View/**/*.php',
    './routes/**/*.php',
    './storage/framework/views/*.php',
  ],
  theme: {
    extend: {
      colors: {
        brand: {
          teal: '#0E6973',
          aqua: '#118C8C',
          mint: '#BAD9CE',
          sun: '#F2BB16',
          ochre: '#BF820F',
        },
        primary: '#118C8C',
        secondary: '#0E6973',
        accent: '#F2BB16',
        muted: '#BAD9CE',
        warn: '#BF820F',
      },
      fontFamily: {
        sans: ['Instrument Sans', ...defaultTheme.fontFamily.sans],
      },
      container: { center: true, padding: '1rem' },
    },
  },
  plugins: [forms, typography, aspectRatio],
}
