// ==========================================================================

// Social Feed Plugin for Craft CMS
// Author: Verbb - https://verbb.io/

// ==========================================================================

// @codekit-prepend "_flexmasonry.js"

FlexMasonry.init('[data-social-feed]', {
    responsive: true,
    breakpointCols: {
        'min-width: 1500px': 4,
        'min-width: 768px': 3,
        'min-width: 576px': 2,
    },
});
