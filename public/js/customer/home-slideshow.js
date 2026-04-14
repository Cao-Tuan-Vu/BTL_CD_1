(() => {
    "use strict";

    const ROTATION_INTERVAL_MS = 5000;
    const FADE_DURATION_MS = 1500;

    document.addEventListener("DOMContentLoaded", () => {
        const hero = document.getElementById("home-hero-slideshow");

        if (!hero) {
            return;
        }

        const layerA = hero.querySelector(".bg1");
        const layerB = hero.querySelector(".bg2");

        if (!layerA || !layerB) {
            return;
        }

        let slides = [];

        try {
            const slideSource = hero.dataset.slides || "[]";
            slides = JSON.parse(slideSource).filter((item) => typeof item === "string" && item.trim() !== "");
        } catch (error) {
            slides = [];
        }

        if (slides.length === 0) {
            return;
        }

        const layers = [layerA, layerB];
        let currentSlideIndex = 0;
        let currentLayerIndex = 0;

        const setLayerImage = (layer, url) => {
            layer.style.backgroundImage = `url('${url}')`;
        };

        const preloadImage = (url) => {
            return new Promise((resolve) => {
                const image = new Image();
                let resolved = false;

                const done = (result) => {
                    if (resolved) {
                        return;
                    }

                    resolved = true;
                    resolve(result);
                };

                image.onload = () => {
                    if (typeof image.decode === "function") {
                        image.decode().catch(() => {}).finally(() => done(url));

                        return;
                    }

                    done(url);
                };
                image.onerror = () => done(null);
                image.decoding = "async";
                image.loading = "eager";
                image.src = url;

                if (image.complete && image.naturalWidth > 0) {
                    if (typeof image.decode === "function") {
                        image.decode().catch(() => {}).finally(() => done(url));

                        return;
                    }

                    done(url);
                }
            });
        };

        const preloadSlides = async (urls) => {
            const loaded = await Promise.all(urls.map((url) => preloadImage(url)));

            return loaded.filter((item) => typeof item === "string" && item.trim() !== "");
        };

        const initializeSlideshow = (readySlides) => {
            slides = readySlides;

            setLayerImage(layers[currentLayerIndex], slides[currentSlideIndex]);
            layers[currentLayerIndex].classList.add("active");

            const inactiveLayerIndex = (currentLayerIndex + 1) % layers.length;
            const nextInitialIndex = (currentSlideIndex + 1) % slides.length;
            setLayerImage(layers[inactiveLayerIndex], slides[nextInitialIndex]);
            hero.classList.add("is-ready");

            if (slides.length < 2) {
                return;
            }

            window.setInterval(() => {
                const nextSlideIndex = (currentSlideIndex + 1) % slides.length;
                const nextLayerIndex = (currentLayerIndex + 1) % layers.length;

                const nextLayer = layers[nextLayerIndex];
                const currentLayer = layers[currentLayerIndex];

                setLayerImage(nextLayer, slides[nextSlideIndex]);
                nextLayer.classList.add("active");
                currentLayer.classList.remove("active");

                currentSlideIndex = nextSlideIndex;
                currentLayerIndex = nextLayerIndex;

                const standbyLayerIndex = (nextLayerIndex + 1) % layers.length;
                const preloadAheadIndex = (nextSlideIndex + 1) % slides.length;

                window.setTimeout(() => {
                    if (!layers[standbyLayerIndex].classList.contains("active")) {
                        setLayerImage(layers[standbyLayerIndex], slides[preloadAheadIndex]);
                    }
                }, FADE_DURATION_MS + 80);
            }, ROTATION_INTERVAL_MS);
        };

        preloadSlides(slides)
            .then((preloadedSlides) => {
                const readySlides = preloadedSlides.length > 0 ? preloadedSlides : slides;

                initializeSlideshow(readySlides);
            })
            .catch(() => {
                initializeSlideshow(slides);
            });
    });
})();
