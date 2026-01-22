/**
 * Xebia AI Summary Generator - Frontend View Script
 *
 * Uses the WordPress Interactivity API for reactive state management
 * without a full page refresh. This demonstrates modern WordPress
 * frontend development techniques.
 *
 * @package Xebia\AuthorityEngine
 * @see https://developer.wordpress.org/block-editor/reference-guides/interactivity-api/
 */

import { store, getContext, getElement } from '@wordpress/interactivity';

/**
 * Store definition for xebia/ai-summary
 *
 * The Interactivity API provides reactive state management similar
 * to Vue or Alpine.js, but integrated with WordPress.
 */
const { state, actions } = store('xebia/ai-summary', {
    /**
     * Reactive state
     */
    state: {
        isLoading: false,
        summary: '',
        error: '',
        traceability: null,
        showTraceability: false,

        /**
         * Derived state: formatted traceability data
         */
        get traceabilityFormatted() {
            const ctx = getContext();
            if (!state.traceability) {
                return 'No traceability data available';
            }

            try {
                const data = {
                    request: JSON.parse(state.traceability.requestPayload || '{}'),
                    response: JSON.parse(state.traceability.responseData || '{}'),
                    duration: `${state.traceability.durationMs?.toFixed(2)}ms`,
                    timestamp: state.traceability.timestamp,
                };
                return JSON.stringify(data, null, 2);
            } catch {
                return JSON.stringify(state.traceability, null, 2);
            }
        },
    },

    /**
     * Actions that can modify state
     */
    actions: {
        /**
         * Generate AI summary for the current post
         */
        async generateSummary() {
            const ctx = getContext();

            if (state.isLoading) return;

            state.isLoading = true;
            state.error = '';

            try {
                const response = await fetch(`${ctx.restUrl}generate-summary/${ctx.postId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-WP-Nonce': ctx.nonce,
                    },
                });

                const data = await response.json();

                if (data.success) {
                    state.summary = data.data.summary;
                    state.traceability = data.data.traceability;
                    state.error = '';
                } else {
                    state.error = data.error || 'Failed to generate summary';
                    state.traceability = data.traceability;
                }
            } catch (err) {
                state.error = err.message || 'Network error occurred';
            } finally {
                state.isLoading = false;
            }
        },

        /**
         * Toggle traceability panel visibility
         */
        toggleTraceability() {
            state.showTraceability = !state.showTraceability;
        },

        /**
         * Clear error state
         */
        clearError() {
            state.error = '';
        },

        /**
         * Initialize block with existing summary from context
         */
        init() {
            const ctx = getContext();
            if (ctx.summary) {
                state.summary = ctx.summary;
            }
        },
    },

    /**
     * Callbacks for lifecycle events
     */
    callbacks: {
        /**
         * Run on element mount
         */
        onMount() {
            actions.init();
        },
    },
});

export { state, actions };
