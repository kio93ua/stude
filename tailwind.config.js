import defaultTheme from 'tailwindcss/defaultTheme'
import forms from '@tailwindcss/forms'
import typography from '@tailwindcss/typography'
import aspectRatio from '@tailwindcss/aspect-ratio'

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
          teal: '#06757C',
          aqua: '#108C8C',
          mint: '#B1DED7',
          sun: '#F1BC16',
          ochre: '#BF820F',
        },
        primary: '#108C8C',
        secondary: '#06757C',
        accent: '#F1BC16',
        muted: '#B1DED7',
        warn: '#BF820F',
      },
      fontFamily: {
        sans: ['var(--font-body)'],
        display: ['var(--font-head)'],
      },
      container: { center: true, padding: '1rem' },
    },
  },
  plugins: [forms, typography, aspectRatio],
}
