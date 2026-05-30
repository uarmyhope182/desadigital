module.exports = {
  darkMode: 'class',
  content: ['./**/*.{php,html}'],
  theme: {
    extend: {
      colors: {
        // Bright Rustic Village Interface - Color Palette
        // Background colors: cream, beige, warm white
        'ghibli-bg-cream': '#FDFBF7',
        'ghibli-bg-beige': '#FAF6F0',
        'ghibli-bg-warm': '#FFF8EF',
        
        // Primary color: Sage Green
        'ghibli-sage': '#9CAF88',
        'ghibli-sage-dark': '#7A8F64',
        'ghibli-sage-light': '#B5C4A3',
        
        // Accent color: Soft Peach
        'ghibli-peach': '#F6C7A1',
        'ghibli-peach-dark': '#F0B885',
        
        // Sky color: Pastel Sky Blue
        'ghibli-sky': '#B9DCFF',
        
        // Text colors: dark charcoal, warm dark brown, deep gray
        'ghibli-text-dark': '#3A312B',
        'ghibli-text-soft': '#2F2F2F',
        'ghibli-text-light': '#444444',
        
        // Border and shadow colors
        'ghibli-border': '#E8E0D5',
        'ghibli-shadow': 'rgba(58, 49, 43, 0.08)',
        'ghibli-shadow-hover': 'rgba(58, 49, 43, 0.12)',
        
        // Status colors - Pastel
        'ghibli-status-pending': '#F6C7A1',
        'ghibli-status-processing': '#B9DCFF',
        'ghibli-status-approved': '#9CAF88',
        'ghibli-status-rejected': '#E8C5C5',
        
        // Alert colors
        'ghibli-success-bg': '#F0F7ED',
        'ghibli-success-border': '#C5E0B4',
        'ghibli-error-bg': '#FFF5F5',
        'ghibli-error-border': '#E8C5C5',
        'ghibli-error-text': '#8B5E5E',
        'ghibli-info-bg': '#F0F4FA',
        'ghibli-info-border': '#C5D5E8',
        
        // Legacy colors preserved for backward compatibility
        primary: '#9CAF88',
        secondary: '#F6C7A1',
        tertiary: '#B9DCFF',
        error: '#E8C5C5',
        background: '#FAF6F0',
        surface: '#FDFBF7',
        outline: '#E8E0D5',
        'outline-variant': '#F0E8DC',
        'surface-container': '#FDFBF7',
        'surface-container-low': '#FAF6F0',
        'surface-container-lowest': '#FFFFFF',
        'surface-container-high': '#FDFBF7',
        'surface-container-highest': '#FAF6F0',
        'surface-bright': '#FDFBF7',
        'surface-dim': '#E8E0D5',
        'surface-variant': '#FAF6F0',
        'surface-tint': '#9CAF88',
        'surface-fixed': '#FFFFFF',
        'surface-fixed-dim': '#FDFBF7',
        'on-background': '#3A312B',
        'on-surface': '#3A312B',
        'on-surface-variant': '#5C524A',
        'on-secondary': '#3A312B',
        'on-secondary-container': '#3A312B',
        'on-secondary-fixed': '#3A312B',
        'on-secondary-fixed-variant': '#5C524A',
        'on-primary': '#FFFFFF',
        'on-primary-fixed': '#FFFFFF',
        'on-primary-fixed-variant': '#FFFFFF',
        'on-tertiary': '#3A312B',
        'on-tertiary-container': '#FFFFFF',
        'on-tertiary-fixed': '#3A312B',
        'inverse-primary': '#B5C4A3',
        'inverse-surface': '#3A312B',
        'inverse-on-surface': '#FDFBF7',
        'primary-container': '#9CAF88',
        'primary-fixed': '#B5C4A3',
        'primary-fixed-dim': '#7A8F64',
        'secondary-container': '#F6C7A1',
        'secondary-fixed': '#F6C7A1',
        'secondary-fixed-dim': '#F0B885',
        'tertiary-container': '#B9DCFF',
        'tertiary-fixed': '#B9DCFF',
        'tertiary-fixed-dim': '#9EC8E8',
        'error-container': '#E8C5C5',
        'on-error': '#FFFFFF',
        'on-error-container': '#8B5E5E'
      },
      borderRadius: {
        DEFAULT: '16px',
        sm: '12px',
        lg: '24px',
        xl: '28px',
        '2xl': '32px',
        full: '9999px',
        card: '24px',
        button: '40px'
      },
      spacing: {
        xs: '4px',
        base: '8px',
        sm: '12px',
        md: '24px',
        lg: '48px',
        xl: '80px',
        gutter: '24px',
        'margin-mobile': '16px',
        'container-max': '1200px'
      },
      fontFamily: {
        'headline-md': ['Bricolage Grotesque', 'Poppins', 'sans-serif'],
        'display-lg': ['Bricolage Grotesque', 'Poppins', 'sans-serif'],
        'body-md': ['Inter', 'sans-serif'],
        'headline-sm': ['Poppins', 'sans-serif'],
        'body-lg': ['Inter', 'sans-serif'],
        'label-md': ['Poppins', 'sans-serif'],
        'display-lg-mobile': ['Bricolage Grotesque', 'Poppins', 'sans-serif'],
        caption: ['Inter', 'sans-serif'],
        'ghibli-heading': ['Bricolage Grotesque', 'Poppins', 'sans-serif'],
        'ghibli-body': ['Inter', 'sans-serif']
      },
      fontSize: {
        'headline-md': ['24px', { lineHeight: '1.3', fontWeight: '600' }],
        'display-lg': ['48px', { lineHeight: '1.2', letterSpacing: '-0.02em', fontWeight: '700' }],
        'body-md': ['16px', { lineHeight: '1.6', fontWeight: '400' }],
        'headline-sm': ['20px', { lineHeight: '1.4', fontWeight: '600' }],
        'body-lg': ['18px', { lineHeight: '1.6', fontWeight: '400' }],
        'label-md': ['14px', { lineHeight: '1.4', letterSpacing: '0.01em', fontWeight: '500' }],
        'display-lg-mobile': ['32px', { lineHeight: '1.2', fontWeight: '700' }],
        caption: ['12px', { lineHeight: '1.4', fontWeight: '400' }]
      },
      backgroundImage: {
        // Background images for Bright Rustic Village Interface
        'ghibli-sky-layer': "url('../assets/images/05-sky-cloud-layers.png')",
        'ghibli-hero': "url('../assets/images/01-hero-main-background.png')",
        'ghibli-rice-fields': "url('../assets/images/rice-fields.png')",
        'ghibli-sunset-footer': "url('../assets/images/05-sunset-footer-landscape.png')",
        'ghibli-village-entrance': "url('../assets/images/11-village-entrance.png')",
        'ghibli-river-stream': "url('../assets/images/12-river-stream-scene.png')",
        'ghibli-atmosphere': "url('../assets/images/06-atmospheric-elements.png')"
      },
      boxShadow: {
        'ghibli-card': '0 4px 12px rgba(58, 49, 43, 0.06)',
        'ghibli-card-hover': '0 12px 24px rgba(58, 49, 43, 0.1)',
        'ghibli-modal': '0 20px 40px rgba(58, 49, 43, 0.12)',
        'ghibli-button': '0 2px 8px rgba(156, 175, 136, 0.2)',
        'ghibli-nav': '0 2px 12px rgba(58, 49, 43, 0.06)',
        'ghibli-input-focus': '0 0 0 3px rgba(156, 175, 136, 0.2)'
      },
      animation: {
        // Simplified animations - natural and subtle
        'breathe': 'breathe 4s ease-in-out infinite',
        'float-jiji': 'floatJiji 4s ease-in-out infinite',
        'float-cloud': 'floatCloud 25s ease-in-out infinite',
        'float-cloud-slow': 'floatCloudSlow 35s ease-in-out infinite',
        'fall-leaf': 'fallLeaf 8s ease-in-out infinite',
        'sway': 'sway 3s ease-in-out infinite',
        'slide-down': 'slideDown 0.3s ease',
        'fade-out': 'fadeOut 0.5s ease',
        'float': 'float 6s ease-in-out infinite',
        'susuwatari-run': 'susuwatariRun 2s ease-in-out infinite'
      },
      keyframes: {
        breathe: {
          '0%, 100%': { transform: 'scale(1)' },
          '50%': { transform: 'scale(1.02)' }
        },
        floatJiji: {
          '0%, 100%': { transform: 'translateY(0px)' },
          '50%': { transform: 'translateY(-6px)' }
        },
        floatCloud: {
          '0%': { transform: 'translateX(-100px) translateY(0px)', opacity: '0' },
          '10%': { opacity: '0.5' },
          '90%': { opacity: '0.5' },
          '100%': { transform: 'translateX(100vw) translateY(20px)', opacity: '0' }
        },
        floatCloudSlow: {
          '0%': { transform: 'translateX(-100px) translateY(0px)', opacity: '0' },
          '10%': { opacity: '0.4' },
          '90%': { opacity: '0.4' },
          '100%': { transform: 'translateX(100vw) translateY(-10px)', opacity: '0' }
        },
        fallLeaf: {
          '0%': { transform: 'translateY(-10vh) rotate(0deg)', opacity: '0' },
          '10%': { opacity: '0.4' },
          '90%': { opacity: '0.4' },
          '100%': { transform: 'translateY(110vh) rotate(360deg)', opacity: '0' }
        },
        sway: {
          '0%, 100%': { transform: 'rotate(-1deg)' },
          '50%': { transform: 'rotate(1deg)' }
        },
        slideDown: {
          '0%': { transform: 'translateX(-50%) translateY(-100px)', opacity: '0' },
          '100%': { transform: 'translateX(-50%) translateY(0)', opacity: '1' }
        },
        fadeOut: {
          '0%': { opacity: '1' },
          '100%': { opacity: '0' }
        },
        float: {
          '0%, 100%': { transform: 'translateY(0px)' },
          '50%': { transform: 'translateY(-8px)' }
        },
        susuwatariRun: {
          '0%, 100%': { transform: 'translateX(0) translateY(0)' },
          '50%': { transform: 'translateX(5px) translateY(-2px)' }
        }
      }
    }
  }
}