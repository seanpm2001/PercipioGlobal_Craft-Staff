module.exports = {
    content: [
        './src/**/*.{vue,js,ts}',
    ],
    safelist: [],
    theme: {

        // Extend the default Tailwind config here
        extend: {
            colors: {
                gray: {
                    50: '#fbfcfe',
                    500: '#607d9f',
                    800: '#33404d',
                }
            },
            display: {
                    'hide': 'display:none'
            },
        },

    },
    important: true,
    plugins: [
        require('@tailwindcss/typography'),
        require('@tailwindcss/forms'),
        require('@tailwindcss/line-clamp'),
        require('@tailwindcss/aspect-ratio'),
    ],
};