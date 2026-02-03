import { ref, reactive, watch, onUnmounted } from 'vue';

/**
 * Composable for tracking connector positions based on DOM element measurements.
 * Handles ResizeObserver setup/cleanup and calculates positions relative to .node-card-wrapper.
 *
 * Two usage modes:
 * 1. Single element: Pass elementRef directly, get position from `positions.value.default`
 * 2. Multiple elements: Use registerElement/unregisterElement, get positions by ID
 *
 * @param {Ref<HTMLElement|null>} containerRef - Ref to the container element to observe for resize
 * @param {Ref<HTMLElement|null>} elementRef - Optional ref for single-element mode
 * @returns {Object} { positions, registerElement, unregisterElement, recalculate }
 */
export function useConnectorPositionTracking(containerRef, elementRef = null) {
    // Reactive map of element ID → top position in px
    const positions = ref({});

    // Element registry for multiple elements mode
    const elements = reactive(new Map());

    // Track unmounted state to prevent observer callbacks after cleanup
    let isUnmounted = false;

    // ResizeObserver instance
    let resizeObserver = null;

    // Debounce timer for recalculation
    let recalculateTimer = null;

    /**
     * Calculate the vertical center position of an element relative to .node-card-wrapper
     */
    const calculateElementPosition = (element) => {
        if (!element || !containerRef.value) return null;

        const cardWrapper = containerRef.value.closest('.node-card-wrapper');
        if (!cardWrapper) return null;

        // Calculate offset using DOM offset properties (not affected by transforms)
        let top = element.offsetTop + (element.offsetHeight / 2);
        let el = element.offsetParent;

        // Walk up the offset parent chain until we reach the card wrapper
        while (el && el !== cardWrapper && !el.classList?.contains('node-card-wrapper')) {
            top += el.offsetTop;
            el = el.offsetParent;
        }

        return top;
    };

    /**
     * Recalculate all tracked element positions
     */
    const recalculatePositions = () => {
        if (isUnmounted || !containerRef.value) return;

        const newPositions = {};

        // Handle single element mode
        if (elementRef?.value) {
            const pos = calculateElementPosition(elementRef.value);
            if (pos !== null) {
                newPositions.default = pos;
            }
        }

        // Handle multiple elements mode
        elements.forEach((element, id) => {
            if (element) {
                const pos = calculateElementPosition(element);
                if (pos !== null) {
                    newPositions[id] = pos;
                }
            }
        });

        positions.value = newPositions;
    };

    /**
     * Schedule a debounced recalculation (10ms delay to batch DOM changes)
     */
    const scheduleRecalculate = () => {
        if (recalculateTimer) clearTimeout(recalculateTimer);
        recalculateTimer = setTimeout(() => {
            recalculatePositions();
        }, 10);
    };

    /**
     * Register an element to track (for multiple elements mode)
     */
    const registerElement = (id, element) => {
        elements.set(id, element);
        scheduleRecalculate();
    };

    /**
     * Unregister an element (for multiple elements mode)
     */
    const unregisterElement = (id) => {
        elements.delete(id);
        scheduleRecalculate();
    };

    /**
     * Get position for an element by ID, with fallback
     */
    const getPosition = (id = 'default', fallback = 50) => {
        return positions.value[id] ?? fallback;
    };

    // Setup ResizeObserver when container is available
    watch(containerRef, (container) => {
        if (resizeObserver) {
            resizeObserver.disconnect();
            resizeObserver = null;
        }
        if (container && !isUnmounted) {
            resizeObserver = new ResizeObserver(() => {
                if (!isUnmounted) {
                    scheduleRecalculate();
                }
            });
            resizeObserver.observe(container);
            // Initial calculation
            scheduleRecalculate();
        }
    }, { immediate: true });

    // Cleanup on unmount
    onUnmounted(() => {
        isUnmounted = true;
        if (resizeObserver) {
            resizeObserver.disconnect();
            resizeObserver = null;
        }
        if (recalculateTimer) {
            clearTimeout(recalculateTimer);
        }
    });

    return {
        positions,
        registerElement,
        unregisterElement,
        recalculate: scheduleRecalculate,
        getPosition,
    };
}
