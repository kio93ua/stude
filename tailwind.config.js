import defaultTheme from 'tailwindcss/defaultTheme'
import forms from '@tailwindcss/forms'
import typography from '@tailwindcss/typography'
import aspectRatio from '@tailwindcss/aspect-ratio'

/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    './storage/framework/views/*.php',
    './resources/views/**/*.blade.php',
    './resources/js/**/*.{js,vue}',
  ],
  theme: {
    extend: {
      // КАСТОМНІ КОЛЬОРИ з твоєї палітри
      colors: {
        brand: {
          teal: '#0E6973', // глибокий бірюзовий
          aqua: '#118C8C', // насичена бірюза
          mint: '#BAD9CE', // м’яка "вода"
          sun: '#F2BB16', // лимон/сонце
          ochre: '#BF820F', // теплий охристий
        },
        // зручні псевдоніми-ролі (можеш використовувати bg-primary, text-accent тощо)
        primary: '#118C8C',
        secondary: '#0E6973',
        accent: '#F2BB16',
        muted: '#BAD9CE',
        warn: '#BF820F',
      },

      fontFamily: {
        sans: ['Instrument Sans', ...defaultTheme.fontFamily.sans],
      },
    },
  },
  plugins: [forms, typography, aspectRatio],
}
