    <!DOCTYPE html>
    <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">

            <title>Sistema de Gestion de Gastos</title>

            <!-- Fuentes -->
            <link rel="preconnect" href="https://fonts.bunny.net">
            <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
            <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
            <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
            <link rel="preconnect" href="https://fonts.googleapis.com">
            <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
            <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Readex+Pro:wght@160..700&family=Roboto+Mono:ital,wght@0,100..700;1,100..700&family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">

            <!-- Estilos -->
            <style>
                /* ! tailwindcss v3.4.1 | MIT License | https://tailwindcss.com */*,::after,::before{box-sizing:border-box;border-width:0;border-style:solid;border-color:#e5e7eb}::after,::before{--tw-content:''}:host,html{line-height:1.5;-webkit-text-size-adjust:100%;-moz-tab-size:4;tab-size:4;font-family:Figtree, ui-sans-serif, system-ui, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji;font-feature-settings:normal;font-variation-settings:normal;-webkit-tap-highlight-color:transparent}body{margin:0;line-height:inherit}hr{height:0;color:inherit;border-top-width:1px}abbr:where([title]){-webkit-text-decoration:underline dotted;text-decoration:underline dotted}h1,h2,h3,h4,h5,h6{font-size:inherit;font-weight:inherit}a{color:inherit;text-decoration:inherit}b,strong{font-weight:bolder}code,kbd,pre,samp{font-family:ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;font-feature-settings:normal;font-variation-settings:normal;font-size:1em}small{font-size:80%}sub,sup{font-size:75%;line-height:0;position:relative;vertical-align:baseline}sub{bottom:-.25em}sup{top:-.5em}table{text-indent:0;border-color:inherit;border-collapse:collapse}button,input,optgroup,select,textarea{font-family:inherit;font-feature-settings:inherit;font-variation-settings:inherit;font-size:100%;font-weight:inherit;line-height:inherit;color:inherit;margin:0;padding:0}button,select{text-transform:none}[type=button],[type=reset],[type=submit],button{-webkit-appearance:button;background-color:transparent;background-image:none}:-moz-focusring{outline:auto}:-moz-ui-invalid{box-shadow:none}progress{vertical-align:baseline}::-webkit-inner-spin-button,::-webkit-outer-spin-button{height:auto}[type=search]{-webkit-appearance:textfield;outline-offset:-2px}::-webkit-search-decoration{-webkit-appearance:none}::-webkit-file-upload-button{-webkit-appearance:button;font:inherit}summary{display:list-item}blockquote,dd,dl,figure,h1,h2,h3,h4,h5,h6,hr,p,pre{margin:0}fieldset{margin:0;padding:0}legend{padding:0}menu,ol,ul{list-style:none;margin:0;padding:0}dialog{padding:0}textarea{resize:vertical}input::placeholder,textarea::placeholder{opacity:1;color:#9ca3af}[role=button],button{cursor:pointer}:disabled{cursor:default}audio,canvas,embed,iframe,img,object,svg,video{display:block;vertical-align:middle}img,video{max-width:100%;height:auto}[hidden]{display:none}*, ::before, ::after{--tw-border-spacing-x:0;--tw-border-spacing-y:0;--tw-translate-x:0;--tw-translate-y:0;--tw-rotate:0;--tw-skew-x:0;--tw-skew-y:0;--tw-scale-x:1;--tw-scale-y:1;--tw-pan-x: ;--tw-pan-y: ;--tw-pinch-zoom: ;--tw-scroll-snap-strictness:proximity;--tw-gradient-from-position: ;--tw-gradient-via-position: ;--tw-gradient-to-position: ;--tw-ordinal: ;--tw-slashed-zero: ;--tw-numeric-figure: ;--tw-numeric-spacing: ;--tw-numeric-fraction: ;--tw-ring-inset: ;--tw-ring-offset-width:0px;--tw-ring-offset-color:#fff;--tw-ring-color:rgb(59 130 246 / 0.5);--tw-ring-offset-shadow:0 0 #0000;--tw-ring-shadow:0 0 #0000;--tw-shadow:0 0 #0000;--tw-shadow-colored:0 0 #0000;--tw-blur: ;--tw-brightness: ;--tw-contrast: ;--tw-grayscale: ;--tw-hue-rotate: ;--tw-invert: ;--tw-saturate: ;--tw-sepia: ;--tw-drop-shadow: ;--tw-backdrop-blur: ;--tw-backdrop-brightness: ;--tw-backdrop-contrast: ;--tw-backdrop-grayscale: ;--tw-backdrop-hue-rotate: ;--tw-backdrop-invert: ;--tw-backdrop-opacity: ;--tw-backdrop-saturate: ;--tw-backdrop-sepia: }::backdrop{--tw-border-spacing-x:0;--tw-border-spacing-y:0;--tw-translate-x:0;--tw-translate-y:0;--tw-rotate:0;--tw-skew-x:0;--tw-skew-y:0;--tw-scale-x:1;--tw-scale-y:1;--tw-pan-x: ;--tw-pan-y: ;--tw-pinch-zoom: ;--tw-scroll-snap-strictness:proximity;--tw-gradient-from-position: ;--tw-gradient-via-position: ;--tw-gradient-to-position: ;--tw-ordinal: ;--tw-slashed-zero: ;--tw-numeric-figure: ;--tw-numeric-spacing: ;--tw-numeric-fraction: ;--tw-ring-inset: ;--tw-ring-offset-width:0px;--tw-ring-offset-color:#fff;--tw-ring-color:rgb(59 130 246 / 0.5);--tw-ring-offset-shadow:0 0 #0000;--tw-ring-shadow:0 0 #0000;--tw-shadow:0 0 #0000;--tw-shadow-colored:0 0 #0000;--tw-blur: ;--tw-brightness: ;--tw-contrast: ;--tw-grayscale: ;--tw-hue-rotate: ;--tw-invert: ;--tw-saturate: ;--tw-sepia: ;--tw-drop-shadow: ;--tw-backdrop-blur: ;--tw-backdrop-brightness: ;--tw-backdrop-contrast: ;--tw-backdrop-grayscale: ;--tw-backdrop-hue-rotate: ;--tw-backdrop-invert: ;--tw-backdrop-opacity: ;--tw-backdrop-saturate: ;--tw-backdrop-sepia: }.absolute{position:absolute}.relative{position:relative}.-left-20{left:-5rem}.top-0{top:0px}.-bottom-16{bottom:-4rem}.-left-16{left:-4rem}.-mx-3{margin-left:-0.75rem;margin-right:-0.75rem}.mt-4{margin-top:1rem}.mt-6{margin-top:1.5rem}.flex{display:flex}.grid{display:grid}.hidden{display:none}.aspect-video{aspect-ratio:16 / 9}.size-12{width:3rem;height:3rem}.size-5{width:1.25rem;height:1.25rem}.size-6{width:1.5rem;height:1.5rem}.h-12{height:3rem}.h-40{height:10rem}.h-full{height:100%}.min-h-screen{min-height:100vh}.w-full{width:100%}.w-\[calc\(100\%\+8rem\)\]{width:calc(100% + 8rem)}.w-auto{width:auto}.max-w-\[877px\]{max-width:877px}.max-w-2xl{max-width:42rem}.flex-1{flex:1 1 0%}.shrink-0{flex-shrink:0}.grid-cols-2{grid-template-columns:repeat(2, minmax(0, 1fr))}.flex-col{flex-direction:column}.items-start{align-items:flex-start}.items-center{align-items:center}.items-stretch{align-items:stretch}.justify-end{justify-content:flex-end}.justify-center{justify-content:center}.gap-2{gap:0.5rem}.gap-4{gap:1rem}.gap-6{gap:1.5rem}.self-center{align-self:center}.overflow-hidden{overflow:hidden}.rounded-\[10px\]{border-radius:10px}.rounded-full{border-radius:9999px}.rounded-lg{border-radius:0.5rem}.rounded-md{border-radius:0.375rem}.rounded-sm{border-radius:0.125rem}.bg-\[\#FF2D20\]\/10{background-color:rgb(255 45 32 / 0.1)}.bg-white{--tw-bg-opacity:1;background-color:rgb(255 255 255 / var(--tw-bg-opacity))}.bg-gradient-to-b{background-image:linear-gradient(to bottom, var(--tw-gradient-stops))}.from-transparent{--tw-gradient-from:transparent var(--tw-gradient-from-position);--tw-gradient-to:rgb(0 0 0 / 0) var(--tw-gradient-to-position);--tw-gradient-stops:var(--tw-gradient-from), var(--tw-gradient-to)}.via-white{--tw-gradient-to:rgb(255 255 255 / 0)  var(--tw-gradient-to-position);--tw-gradient-stops:var(--tw-gradient-from), #fff var(--tw-gradient-via-position), var(--tw-gradient-to)}.to-white{--tw-gradient-to:#fff var(--tw-gradient-to-position)}.stroke-\[\#FF2D20\]{stroke:#FF2D20}.object-cover{object-fit:cover}.object-top{object-position:top}.p-6{padding:1.5rem}.px-6{padding-left:1.5rem;padding-right:1.5rem}.py-10{padding-top:2.5rem;padding-bottom:2.5rem}.px-3{padding-left:0.75rem;padding-right:0.75rem}.py-16{padding-top:4rem;padding-bottom:4rem}.py-2{padding-top:0.5rem;padding-bottom:0.5rem}.pt-3{padding-top:0.75rem}.text-center{text-align:center}.font-sans{font-family:Figtree, ui-sans-serif, system-ui, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji}.text-sm{font-size:0.875rem;line-height:1.25rem}.text-sm\/relaxed{font-size:0.875rem;line-height:1.625}.text-xl{font-size:1.25rem;line-height:1.75rem}.font-semibold{font-weight:600}.text-black{--tw-text-opacity:1;color:rgb(0 0 0 / var(--tw-text-opacity))}.text-white{--tw-text-opacity:1;color:rgb(255 255 255 / var(--tw-text-opacity))}.underline{-webkit-text-decoration-line:underline;text-decoration-line:underline}.antialiased{-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale}.shadow-\[0px_14px_34px_0px_rgba\(0\2c 0\2c 0\2c 0\.08\)\]{--tw-shadow:0px 14px 34px 0px rgba(0,0,0,0.08);--tw-shadow-colored:0px 14px 34px 0px var(--tw-shadow-color);box-shadow:var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow)}.ring-1{--tw-ring-offset-shadow:var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);--tw-ring-shadow:var(--tw-ring-inset) 0 0 0 calc(1px + var(--tw-ring-offset-width)) var(--tw-ring-color);box-shadow:var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow, 0 0 #0000)}.ring-transparent{--tw-ring-color:transparent}.ring-white\/\[0\.05\]{--tw-ring-color:rgb(255 255 255 / 0.05)}.drop-shadow-\[0px_4px_34px_rgba\(0\2c 0\2c 0\2c 0\.06\)\]{--tw-drop-shadow:drop-shadow(0px 4px 34px rgba(0,0,0,0.06));filter:var(--tw-blur) var(--tw-brightness) var(--tw-contrast) var(--tw-grayscale) var(--tw-hue-rotate) var(--tw-invert) var(--tw-saturate) var(--tw-sepia) var(--tw-drop-shadow)}.drop-shadow-\[0px_4px_34px_rgba\(0\2c 0\2c 0\2c 0\.25\)\]{--tw-drop-shadow:drop-shadow(0px 4px 34px rgba(0,0,0,0.25));filter:var(--tw-blur) var(--tw-brightness) var(--tw-contrast) var(--tw-grayscale) var(--tw-hue-rotate) var(--tw-invert) var(--tw-saturate) var(--tw-sepia) var(--tw-drop-shadow)}.transition{transition-property:color, background-color, border-color, fill, stroke, opacity, box-shadow, transform, filter, -webkit-text-decoration-color, -webkit-backdrop-filter;transition-property:color, background-color, border-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter, backdrop-filter;transition-property:color, background-color, border-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter, backdrop-filter, -webkit-text-decoration-color, -webkit-backdrop-filter;transition-timing-function:cubic-bezier(0.4, 0, 0.2, 1);transition-duration:150ms}.duration-300{transition-duration:300ms}.selection\:bg-\[\#FF2D20\] *::selection{--tw-bg-opacity:1;background-color:rgb(255 45 32 / var(--tw-bg-opacity))}.selection\:text-white *::selection{--tw-text-opacity:1;color:rgb(255 255 255 / var(--tw-text-opacity))}.selection\:bg-\[\#FF2D20\]::selection{--tw-bg-opacity:1;background-color:rgb(255 45 32 / var(--tw-bg-opacity))}.selection\:text-white::selection{--tw-text-opacity:1;color:rgb(255 255 255 / var(--tw-text-opacity))}.hover\:text-black:hover{--tw-text-opacity:1;color:rgb(0 0 0 / var(--tw-text-opacity))}.hover\:text-black\/70:hover{color:rgb(0 0 0 / 0.7)}.hover\:ring-black\/20:hover{--tw-ring-color:rgb(0 0 0 / 0.2)}.focus\:outline-none:focus{outline:2px solid transparent;outline-offset:2px}.focus-visible\:ring-1:focus-visible{--tw-ring-offset-shadow:var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);--tw-ring-shadow:var(--tw-ring-inset) 0 0 0 calc(1px + var(--tw-ring-offset-width)) var(--tw-ring-color);box-shadow:var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow, 0 0 #0000)}.focus-visible\:ring-\[\#FF2D20\]:focus-visible{--tw-ring-opacity:1;--tw-ring-color:rgb(255 45 32 / var(--tw-ring-opacity))}@media (min-width: 640px){.sm\:size-16{width:4rem;height:4rem}.sm\:size-6{width:1.5rem;height:1.5rem}.sm\:pt-5{padding-top:1.25rem}}@media (min-width: 768px){.md\:row-span-3{grid-row:span 3 / span 3}}@media (min-width: 1024px){.lg\:col-start-2{grid-column-start:2}.lg\:h-16{height:4rem}.lg\:max-w-7xl{max-width:80rem}.lg\:grid-cols-3{grid-template-columns:repeat(3, minmax(0, 1fr))}.lg\:grid-cols-2{grid-template-columns:repeat(2, minmax(0, 1fr))}.lg\:flex-col{flex-direction:column}.lg\:items-end{align-items:flex-end}.lg\:justify-center{justify-content:center}.lg\:gap-8{gap:2rem}.lg\:p-10{padding:2.5rem}.lg\:pb-10{padding-bottom:2.5rem}.lg\:pt-0{padding-top:0px}.lg\:text-\[\#FF2D20\]{--tw-text-opacity:1;color:rgb(255 45 32 / var(--tw-text-opacity))}}@media (prefers-color-scheme: dark){.dark\:block{display:block}.dark\:hidden{display:none}.dark\:bg-black{--tw-bg-opacity:1;background-color:rgb(0 0 0 / var(--tw-bg-opacity))}.dark\:bg-zinc-900{--tw-bg-opacity:1;background-color:rgb(24 24 27 / var(--tw-bg-opacity))}.dark\:via-zinc-900{--tw-gradient-to:rgb(24 24 27 / 0)  var(--tw-gradient-to-position);--tw-gradient-stops:var(--tw-gradient-from), #18181b var(--tw-gradient-via-position), var(--tw-gradient-to)}.dark\:to-zinc-900{--tw-gradient-to:#18181b var(--tw-gradient-to-position)}.dark\:text-white\/50{color:rgb(255 255 255 / 0.5)}.dark\:text-white{--tw-text-opacity:1;color:rgb(255 255 255 / var(--tw-text-opacity))}.dark\:text-white\/70{color:rgb(255 255 255 / 0.7)}.dark\:ring-zinc-800{--tw-ring-opacity:1;--tw-ring-color:rgb(39 39 42 / var(--tw-ring-opacity))}.dark\:hover\:text-white:hover{--tw-text-opacity:1;color:rgb(255 255 255 / var(--tw-text-opacity))}.dark\:hover\:text-white\/70:hover{color:rgb(255 255 255 / 0.7)}.dark\:hover\:text-white\/80:hover{color:rgb(255 255 255 / 0.8)}.dark\:hover\:ring-zinc-700:hover{--tw-ring-opacity:1;--tw-ring-color:rgb(63 63 70 / var(--tw-ring-opacity))}.dark\:focus-visible\:ring-\[\#FF2D20\]:focus-visible{--tw-ring-opacity:1;--tw-ring-color:rgb(255 45 32 / var(--tw-ring-opacity))}.dark\:focus-visible\:ring-white:focus-visible{--tw-ring-opacity:1;--tw-ring-color:rgb(255 255 255 / var(--tw-ring-opacity))}}
            </style>
        </head>
        <body class="font-sans antialiased dark:bg-black dark:text-white/50 m-0">
            <div class="bg-gray-50 text-black/50 dark:bg-black dark:text-white/50">
            <img id="background" class="absolute inset-0 w-full h-full object-cover" src="https://images.pexels.com/photos/10410019/pexels-photo-10410019.jpeg" />
                
            <div class="relative w-full px-6" style="background-color: rgba(0, 0, 0, 0.7) ">
                    <header class="grid grid-cols-2 items-center gap-2 py-6 lg:grid-cols-3">
                            <div class="flex lg:justify-center m-0 lg:col-start-2 px-0" style="transition: border-color 0.3s, transform 0.3s;"
                                onmouseover="this.style.borderColor='white'; this.style.transform='scale(1.3)'"
                                onmouseout="this.style.borderColor='transparent'; this.style.transform='scale(1)'">
                                <!--Logo-->
                                <svg width="80px" height="80px" viewBox="-4.8 -4.8 57.60 57.60" fill="none" xmlns="http://www.w3.org/2000/svg" transform="rotate(0)" stroke="#ffffff"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path fill-rule="evenodd" clip-rule="evenodd" d="M29.2948 17H31.051L31.4434 24.0637C31.5037 25.15 32.4022 26 33.4902 26H40.051L40.3287 31H19.0571L19.3102 26.4453C19.6046 21.1461 23.9874 17 29.2948 17ZM17.3132 26.3344C17.6665 19.9754 22.926 15 29.2948 15H31.051C32.1124 15 32.989 15.8292 33.0479 16.8891L33.4403 23.9528C33.4418 23.9793 33.4637 24 33.4902 24H40.524C41.3201 24 41.9775 24.6219 42.0216 25.4168L42.4136 32.4723C42.4295 32.7589 42.2014 33 41.9144 33H18C17.4259 33 16.9697 32.5177 17.0016 31.9445L17.3132 26.3344Z" fill="#000000"></path> <path fill-rule="evenodd" clip-rule="evenodd" d="M27.6501 20.9957C25.5695 21.5676 23.9603 23.0947 23.3388 25.1273L26.9166 24.263C27.2249 24.1651 27.541 24.0943 27.8608 24.051L27.6501 20.9957ZM27.4623 26.1887C27.8331 26.0637 28.225 25.9998 28.6199 25.9998H29.7321C29.877 25.9998 29.9915 25.8771 29.9816 25.7326L29.5321 19.2154C29.514 18.9532 29.2957 18.7483 29.0332 18.7616C24.7668 18.9779 21.3392 22.1481 21.0759 26.496L21.0409 27.0748C21.0205 27.4103 21.3306 27.6699 21.6574 27.591L27.4623 26.1887Z" fill="#000000"></path> <path fill-rule="evenodd" clip-rule="evenodd" d="M9.97943 7.4436C9.151 7.4436 8.47943 8.11518 8.47943 8.9436C8.47943 9.77203 9.151 10.4436 9.97943 10.4436C10.8079 10.4436 11.4794 9.77203 11.4794 8.9436C11.4794 8.11518 10.8079 7.4436 9.97943 7.4436ZM6.47943 8.9436C6.47943 7.01061 8.04643 5.4436 9.97943 5.4436C11.9124 5.4436 13.4794 7.01061 13.4794 8.9436C13.4794 10.8766 11.9124 12.4436 9.97943 12.4436C8.04643 12.4436 6.47943 10.8766 6.47943 8.9436Z" fill="#000000"></path> <path fill-rule="evenodd" clip-rule="evenodd" d="M12.2892 11.5724C13.89 12.0628 15.4984 12.5619 16.9652 13.2229C18.4666 13.8994 19.7498 14.7162 20.6751 15.794C21.078 16.2631 21.7727 16.3778 22.3078 16.0361C23.2259 15.45 24.2214 14.9766 25.2747 14.6343C26.0416 14.3851 26.4221 13.4395 25.9098 12.7222C22.8349 8.41712 17.5721 7.29333 12.9049 6.29673L12.7291 6.25919C12.4972 6.20965 12.2666 6.16037 12.0368 6.1109C12.7881 6.65766 13.3135 7.49612 13.4465 8.45985C17.5262 9.35293 21.2314 10.3649 23.6553 13.0977C23.0007 13.3635 22.369 13.6741 21.7639 14.0259C20.6369 12.8844 19.2384 12.0535 17.7869 11.3994C16.3539 10.7537 14.8161 10.2579 13.3706 9.81228C13.1936 10.5048 12.8099 11.1147 12.2892 11.5724Z" fill="#000000"></path> <path fill-rule="evenodd" clip-rule="evenodd" d="M6.73489 10.2576L3.66688 23.3278C3.662 23.3486 3.65791 23.3693 3.65458 23.3901L3.24193 25.6752C2.79136 28.1703 4.32855 30.5942 6.77759 31.2504L6.95328 31.2975C8.83697 31.8022 10.8402 31.1358 12.0461 29.6032L14.7636 26.1499C15.3754 25.3723 14.7578 24.2404 13.7728 24.3342L12.0501 24.4983L9.55234 23.9705L8.51501 23.6926L12.1328 11.7021C11.5641 12.1466 10.8543 12.419 10.0819 12.4412L6.65334 23.8044C6.46989 24.4124 6.82576 25.0517 7.43921 25.216L9.12205 25.667L9.14469 25.673L9.16762 25.6779L11.7818 26.2303C11.8932 26.2538 12.0076 26.2603 12.121 26.2495L12.4857 26.2148L10.6709 28.5211C9.89783 29.5035 8.6137 29.9307 7.40621 29.6071L7.23052 29.56C5.66063 29.1394 4.67525 27.5856 4.96407 25.9862L5.37643 23.7027L8.14177 11.922C7.5127 11.5331 7.01601 10.9506 6.73489 10.2576Z" fill="#000000"></path> <path fill-rule="evenodd" clip-rule="evenodd" d="M38.5 36H19.5C18.1193 36 17 37.1193 17 38.5C17 39.8807 18.1193 41 19.5 41H38.5C39.8807 41 41 39.8807 41 38.5C41 37.1193 39.8807 36 38.5 36ZM19.5 34C17.0147 34 15 36.0147 15 38.5C15 40.9853 17.0147 43 19.5 43H38.5C40.9853 43 43 40.9853 43 38.5C43 36.0147 40.9853 34 38.5 34H19.5Z" fill="#000000"></path> <path d="M23 38.5C23 39.3284 22.3284 40 21.5 40C20.6716 40 20 39.3284 20 38.5C20 37.6716 20.6716 37 21.5 37C22.3284 37 23 37.6716 23 38.5Z" fill="#000000"></path> <path d="M28 38.5C28 39.3284 27.3284 40 26.5 40C25.6716 40 25 39.3284 25 38.5C25 37.6716 25.6716 37 26.5 37C27.3284 37 28 37.6716 28 38.5Z" fill="#000000"></path> <path d="M33 38.5C33 39.3284 32.3284 40 31.5 40C30.6716 40 30 39.3284 30 38.5C30 37.6716 30.6716 37 31.5 37C32.3284 37 33 37.6716 33 38.5Z" fill="#000000"></path> <path d="M38 38.5C38 39.3284 37.3284 40 36.5 40C35.6716 40 35 39.3284 35 38.5C35 37.6716 35.6716 37 36.5 37C37.3284 37 38 37.6716 38 38.5Z" fill="#000000"></path> </g></svg>    

                        </div>
                         
                            <nav class="-mx-3 flex flex-1 justify-end bg-black/5"> <!--Barra de navegacion-->
                                <div class="absolute top-0 left-0 rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white">
                                <h2 class="text-xl mt-10" style="color: #F3ETD1; font-family: 'Playfair Display', serif; font-style: italic; text-decoration: underline; font-size: 60px; transition: border-color 0.3s, transform 0.3s;"
                                onmouseover="this.style.transform='scale(1.1)'"
                                onmouseout="this.style.transform='scale(1)'">
                                Traterra 
                                </h2>
                                </div>
                                    
                                        
                                  
                                        <a
                                            href="{{ route('login') }}"  
                                            class="rounded-md px-3 py-2 text-8x1 ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                                        >
                                            Iniciar Sesion
                                        </a>

                                       
                                            <a
                                                href="{{ route('register') }}" 
                                                class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                                            >
                                                Registrarse
                                            </a>
                                       
                                </nav>
                           
                        </header>

                        
                    </div>

                    
            </div>

            <!--Contenedor principal-->
            <div class="mt-20 flex justify-center bottom-center items-center selection:bg-[#FF2D20] selection:text-white">
            <div class="static text-center" 
            style="margin-top: 50px; background-color: rgba(0, 0, 0, 0.7); backdrop-filter: blur(5px); color: white; padding: 20px; border-radius: 10px; margin: 15px;">

                        <div class="flex size-12 shrink-0 items-center justify-center rounded-full bg-[#FF2D20]/10 sm:size-16"
                        style="transition: border-color 0.3s, transform 0.3s;"
                                onmouseover="this.style.borderColor='white'; this.style.transform='scale(1.1)'"
                                onmouseout="this.style.borderColor='transparent'; this.style.transform='scale(1)'">
                            <svg class="size-5 sm:size-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><path fill="#FF2D20" d="M23 4a1 1 0 0 0-1.447-.894L12.224 7.77a.5.5 0 0 1-.448 0L2.447 3.106A1 1 0 0 0 1 4v13.382a1.99 1.99 0 0 0 1.105 1.79l9.448 4.728c.14.065.293.1.447.1.154-.005.306-.04.447-.105l9.453-4.724a1.99 1.99 0 0 0 1.1-1.789V4ZM3 6.023a.25.25 0 0 1 .362-.223l7.5 3.75a.251.251 0 0 1 .138.223v11.2a.25.25 0 0 1-.362.224l-7.5-3.75a.25.25 0 0 1-.138-.22V6.023Zm18 11.2a.25.25 0 0 1-.138.224l-7.5 3.75a.249.249 0 0 1-.329-.099.249.249 0 0 1-.033-.12V9.772a.251.251 0 0 1 .138-.224l7.5-3.75a.25.25 0 0 1 .362.224v11.2Z"/><path fill="#FF2D20" d="m3.55 1.893 8 4.048a1.008 1.008 0 0 0 .9 0l8-4.048a1 1 0 0 0-.9-1.785l-7.322 3.706a.506.506 0 0 1-.452 0L4.454.108a1 1 0 0 0-.9 1.785H3.55Z"/></svg>
                        </div>

                    <h2 class="text-xl font-bold text-black dark:text-white" style="font-size: 40px; margin-top: 50px; transition: border-color 0.3s, transform 0.3s;"
                    onmouseover="this.style.borderColor='white'; this.style.transform='scale(1.1)'"
                    onmouseout="this.style.borderColor='transparent'; this.style.transform='scale(1)'">
                    Sistema <br> <span style="color: #ed9e52; margin-top:5px;">Administrativo</span>
                    </h2>
                    <p class="mt-4 text-sm/relaxed">Sistema gestor de gastos administrativos y operativos.</p>
                    
                    <div class="flex justify-center">
                        <a href="{{ route('login') }}"> <!--Redireccion a la pagina Login-->
                        <button class="mt-5 border-2 p-2 rounded-sm text-white font-bold hover:bg-custom-light hover:border-black hover:shadow-lg transition duration-300" style="color: #4C5444; border-color: #19100D; margin-top: 50px; transition: border-color 0.3s, transform 0.3s;"
                    onmouseover="this.style.borderColor='#AD683C'; this.style.transform='scale(1.1)'"
                    onmouseout="this.style.borderColor='#19100D'; this.style.transform='scale(1)'">
                            Ingresar
                        </button>
                        </a>
                    </div>
                </div>
                
            </div>

            
                    

             <footer class="absolute bottom-0 left-50 justify-center py-16 text-center text-sm text-white dark:text-white/7 bg-bl">
                 Laravel v{{ Illuminate\Foundation\Application::VERSION }} (PHP v{{ PHP_VERSION }})
             </footer>
            
        </body>
    </html>
