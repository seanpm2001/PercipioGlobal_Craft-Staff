module.exports = {
    content: [
        './src/**/*.{vue,js,ts}',
    ],
    safelist: [
        'border',
        'border-b-0',
        'border-l-0',
        'border-r-0',
        'border-gray-200',
        'col-span-3',
        'col-span-4',
        'gap-x-4',
        'gap-y-2',
        'grid-cols-3',
        'line-through',
        'mb-2',
        'mb-6',
        'mt-1',
        'rounded-xl',
        'text-blue-600',
        'pt-6',
        'pt-10',
    ],
    theme: {

        // Extend the default Tailwind config here
        extend: {
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