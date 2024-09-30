// document.addEventListener("DOMContentLoaded", function() {
//     const lazyImages = document.querySelectorAll("img.lazyload");

//     const imageObserver = new IntersectionObserver((entries, observer) => {
//         entries.forEach(entry => {
//             if (entry.isIntersecting) {
//                 const img = entry.target;
//                 img.src = img.dataset.src;
//                 img.classList.remove("lazyload");
//                 imageObserver.unobserve(img);
//             }
//         });
//     });

//     lazyImages.forEach(img => {
//         imageObserver.observe(img);
//     });
// });
document.addEventListener("DOMContentLoaded", function() {
    const lazyImages = document.querySelectorAll("img.lazyload");

    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                const realSrc = img.getAttribute('data-src');
                const spinner = img.nextElementSibling; // Assuming the spinner is the next sibling

                const imgLoader = new Image();
                imgLoader.src = realSrc;
                imgLoader.onload = function() {
                    img.src = realSrc;
                    img.classList.remove("lazyload");

                    // Hide or remove the spinner
                    if (spinner) {
                        spinner.style.display = 'none'; // Hide spinner
                    }

                    imageObserver.unobserve(img);
                };
            }
        });
    });

    lazyImages.forEach(img => {
        imageObserver.observe(img);
    });
});
