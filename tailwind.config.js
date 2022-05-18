const defaultTheme = require('tailwindcss/defaultTheme');

module.exports = {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            colors: {
                'universe': '#000A33',
                'dandelion-50': '#FDF5CF',
                'dandelion-100': '#FBEDA9',
                'dandelion-200': '#F8E483',
                'dandelion-300': '#F4DA5E', // Brand color
                'dandelion-400': '#EFD03A',
                'dandelion-500': '#EAC517',
                'dandelion-600': '#C3A514',
                'dandelion-700': '#9D8512',
                'dandelion-800': '#77650F',
                'dandelion-900': '#52450C',
                'vermilion-50': '#FFDDD5',
                'vermilion-100': '#FFBBAC',
                'vermilion-200': '#FE9A85',
                'vermilion-300': '#FC7A5E',
                'vermilion-400': '#F95A38', // Brand color
                'vermilion-500': '#F53B13',
                'vermilion-600': '#D42E0B',
                'vermilion-700': '#AC270B',
                'vermilion-800': '#841F0A',
                'vermilion-900': '#5D1708',
                'tiara': '#D3DAD9',
                'wedgewood-50': '#CCE4ED',
                'wedgewood-100': '#B0D3E1',
                'wedgewood-200': '#94C2D4',
                'wedgewood-300': '#78B1C6',
                'wedgewood-400': '#5E9FB8',
                'wedgewood-500': '#4B8AA2', // Brand color
                'wedgewood-600': '#407184',
                'wedgewood-700': '#345967',
                'wedgewood-800': '#28414B',
                'wedgewood-900': '#1A2A2F',
                'shiraz-50': '#FFBBC4',
                'shiraz-100': '#FF92A0',
                'shiraz-200': '#FD6B7E',
                'shiraz-300': '#FB445C',
                'shiraz-400': '#F81F3B',
                'shiraz-500': '#E40A26',
                'shiraz-600': '#BB0A21', // Brand color
                'shiraz-700': '#93091B',
                'shiraz-800': '#6B0815',
                'shiraz-900': '#45060E',
                'blush': '#FFF9FB',
            },
            fontFamily: {
                sans: ['Inter var', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography')
    ],
};
