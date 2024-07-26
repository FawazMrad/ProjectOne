import preset from '../../../../vendor/filament/filament/tailwind.config.preset'

/** @type {import('tailwindcss').Config} */


export default {
    presets: [preset],
    content: [
        './app/Filament/**/*.php',
        './resources/views/filament/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
    ],
    theme: {
            colors: {
                //white: '#ffedd5',
                //white: '#d6d3d1',
                white: '#d1d5db',
                //white: '#e9d5ff',
            }
        }



}
