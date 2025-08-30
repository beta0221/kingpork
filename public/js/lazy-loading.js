// Lazy Loading 圖片優化腳本
(function() {
    'use strict';

    // 檢查瀏覽器是否支援 Intersection Observer
    if (!window.IntersectionObserver) {
        // 如果不支援，直接載入所有圖片
        loadAllImages();
        return;
    }

    // 圖片載入觀察器
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                loadImage(img);
                observer.unobserve(img);
            }
        });
    }, {
        // 提前 50px 開始載入圖片
        rootMargin: '50px 0px',
        threshold: 0.01
    });

    // 載入圖片的函數
    function loadImage(img) {
        // 顯示載入動畫
        img.classList.add('loading');
        
        const src = img.dataset.src;
        if (!src) return;

        // 創建新的圖片對象來預載入
        const newImg = new Image();
        
        newImg.onload = function() {
            // 載入成功後設置 src 並移除載入狀態
            img.src = src;
            img.classList.remove('loading');
            img.classList.add('loaded');
            
            // 移除 data-src 屬性
            img.removeAttribute('data-src');
        };
        
        newImg.onerror = function() {
            // 載入失敗時顯示預設圖片
            img.src = '/images/placeholder-error.png';
            img.classList.remove('loading');
            img.classList.add('error');
        };
        
        newImg.src = src;
    }

    // 如果瀏覽器不支援 Intersection Observer，直接載入所有圖片
    function loadAllImages() {
        const lazyImages = document.querySelectorAll('img[data-src]');
        lazyImages.forEach(loadImage);
    }

    // 初始化 lazy loading
    function initLazyLoading() {
        const lazyImages = document.querySelectorAll('img[data-src]');
        
        lazyImages.forEach(img => {
            // 設置預設的佔位圖
            if (!img.src || img.src === '') {
                img.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 300 200"%3E%3Crect fill="%23f0f0f0" width="300" height="200"/%3E%3Ctext fill="%23999" x="50%" y="50%" text-anchor="middle" dy=".35em"%3E載入中...%3C/text%3E%3C/svg%3E';
            }
            
            // 開始觀察圖片
            imageObserver.observe(img);
        });
    }

    // DOM 準備好後初始化
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initLazyLoading);
    } else {
        initLazyLoading();
    }

    // 提供全域函數供手動觸發
    window.LazyLoading = {
        init: initLazyLoading,
        loadImage: loadImage
    };
})();

// 添加 CSS 樣式
const style = document.createElement('style');
style.textContent = `
    img[data-src] {
        transition: opacity 0.3s ease;
        background-color: #f0f0f0;
    }
    
    img.loading {
        opacity: 0.6;
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: loading 1.5s infinite;
    }
    
    img.loaded {
        opacity: 1;
    }
    
    img.error {
        opacity: 0.5;
        background-color: #ffebee;
    }
    
    @keyframes loading {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }
`;
document.head.appendChild(style);