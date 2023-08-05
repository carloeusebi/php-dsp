import { nextTick } from 'vue';

/**
 * Scrolls the modal, where to scroll is decided by the scroll parameter.
 *
 * A value of 0 scrolls to top;
 *
 * A value of -1 scrolls to bottom;
 *
 * Another numerical value scrolls to that height in pixels.
 *
 * @param element The HTML Element to scroll.
 * @param top Where to scroll to in pixel.
 * @param behavior The scroll behavior, either auto, instant, or scroll. Default is smooth
 */
export function useScrollTo(element: HTMLElement, top: number, behavior: 'auto' | 'instant' | 'smooth' = 'smooth') {
	nextTick(() => {
		if (!element) return;
		if (top === -1) top = element.scrollHeight;
		element.scrollTo({ top, behavior });
	});
}
