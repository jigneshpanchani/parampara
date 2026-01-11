/**
 * Responsive Utilities for Vue Components
 * 
 * This file contains utility functions and classes to help make
 * components responsive across the entire application.
 */

// Responsive breakpoints (matching Tailwind CSS)
export const breakpoints = {
    sm: 640,
    md: 768,
    lg: 1024,
    xl: 1280,
    '2xl': 1536
};

// Responsive grid column configurations
export const gridConfigs = {
    // For stat cards/metrics
    stats: {
        default: 1,
        sm: 2,
        lg: 3,
        xl: 4
    },
    
    // For list items
    list: {
        default: 1,
        md: 2,
        lg: 3
    },
    
    // For form layouts
    form: {
        default: 1,
        lg: 2
    },
    
    // For dashboard widgets
    dashboard: {
        default: 1,
        sm: 2,
        lg: 3,
        xl: 4
    }
};

// Responsive spacing configurations
export const spacing = {
    container: {
        mobile: 'px-2 sm:px-4',
        desktop: 'lg:px-8'
    },
    
    padding: {
        small: 'p-2 sm:p-3',
        normal: 'p-4 sm:p-6',
        large: 'p-6 sm:p-8'
    },
    
    margin: {
        small: 'm-2 sm:m-3',
        normal: 'm-4 sm:m-6',
        large: 'm-6 sm:m-8'
    },
    
    gap: {
        small: 'gap-2 sm:gap-3',
        normal: 'gap-4 sm:gap-6',
        large: 'gap-6 sm:gap-8'
    }
};

// Responsive typography configurations
export const typography = {
    heading: {
        h1: 'text-xl sm:text-2xl lg:text-3xl',
        h2: 'text-lg sm:text-xl lg:text-2xl',
        h3: 'text-base sm:text-lg lg:text-xl',
        h4: 'text-sm sm:text-base lg:text-lg'
    },
    
    body: {
        large: 'text-base sm:text-lg',
        normal: 'text-sm sm:text-base',
        small: 'text-xs sm:text-sm'
    }
};

// Common responsive class combinations
export const responsiveClasses = {
    // Card layouts
    card: 'bg-white rounded-lg shadow-sm border p-4 sm:p-6 hover:shadow-md transition-shadow duration-200',
    
    // Button layouts
    button: {
        primary: 'bg-indigo-600 hover:bg-indigo-500 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200',
        secondary: 'bg-gray-200 hover:bg-gray-300 text-gray-900 px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200'
    },
    
    // Table layouts
    table: {
        container: 'overflow-x-auto sm:rounded-lg',
        table: 'w-full text-sm text-left text-gray-500',
        header: 'text-xs text-gray-700 uppercase bg-gray-50',
        cell: 'px-4 py-2.5 whitespace-nowrap'
    },
    
    // Form layouts
    form: {
        container: 'space-y-4 sm:space-y-6',
        group: 'space-y-2',
        label: 'block text-sm font-medium text-gray-700',
        input: 'w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500'
    }
};

// Utility function to get responsive classes
export function getResponsiveClasses(type, variant = 'default') {
    const classes = responsiveClasses[type];
    if (typeof classes === 'object' && classes[variant]) {
        return classes[variant];
    }
    return classes || '';
}

// Utility function to generate grid classes
export function getGridClasses(config = gridConfigs.stats, gap = 'normal') {
    const gapClass = spacing.gap[gap] || spacing.gap.normal;
    
    let classes = ['grid', gapClass];
    
    // Add responsive column classes
    if (config.default) classes.push(`grid-cols-${config.default}`);
    if (config.sm) classes.push(`sm:grid-cols-${config.sm}`);
    if (config.md) classes.push(`md:grid-cols-${config.md}`);
    if (config.lg) classes.push(`lg:grid-cols-${config.lg}`);
    if (config.xl) classes.push(`xl:grid-cols-${config.xl}`);
    if (config['2xl']) classes.push(`2xl:grid-cols-${config['2xl']}`);
    
    return classes.join(' ');
}

// Utility function to check if screen is mobile
export function isMobile() {
    if (typeof window === 'undefined') return false;
    return window.innerWidth < breakpoints.lg;
}

// Utility function to get current breakpoint
export function getCurrentBreakpoint() {
    if (typeof window === 'undefined') return 'sm';
    
    const width = window.innerWidth;
    
    if (width >= breakpoints['2xl']) return '2xl';
    if (width >= breakpoints.xl) return 'xl';
    if (width >= breakpoints.lg) return 'lg';
    if (width >= breakpoints.md) return 'md';
    if (width >= breakpoints.sm) return 'sm';
    
    return 'xs';
}

// Vue composable for responsive behavior
export function useResponsive() {
    const { ref, onMounted, onUnmounted } = require('vue');
    
    const currentBreakpoint = ref(getCurrentBreakpoint());
    const isMobileView = ref(isMobile());
    
    const updateBreakpoint = () => {
        currentBreakpoint.value = getCurrentBreakpoint();
        isMobileView.value = isMobile();
    };
    
    onMounted(() => {
        window.addEventListener('resize', updateBreakpoint);
        updateBreakpoint();
    });
    
    onUnmounted(() => {
        window.removeEventListener('resize', updateBreakpoint);
    });
    
    return {
        currentBreakpoint,
        isMobileView,
        breakpoints,
        gridConfigs,
        spacing,
        typography,
        getResponsiveClasses,
        getGridClasses
    };
}

export default {
    breakpoints,
    gridConfigs,
    spacing,
    typography,
    responsiveClasses,
    getResponsiveClasses,
    getGridClasses,
    isMobile,
    getCurrentBreakpoint,
    useResponsive
};
