/**
 * Xebia AI Summary Generator Block
 *
 * A Gutenberg block that generates AI-powered executive summaries
 * using the OpenAI GPT-4o model. Demonstrates modern WordPress
 * block development with React and the Block Editor API.
 *
 * @package Xebia\AuthorityEngine
 */

import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps } from '@wordpress/block-editor';
import { useState, useCallback } from '@wordpress/element';
import { Button, Spinner, Panel, PanelBody, Notice } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import apiFetch from '@wordpress/api-fetch';
import './editor.scss';

/**
 * Block metadata from block.json
 */
import metadata from '../block.json';

/**
 * AI Icon Component
 */
const AIIcon = () => (
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" width="24" height="24">
        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
    </svg>
);

/**
 * Regenerate Icon Component
 */
const RegenerateIcon = () => (
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" width="16" height="16">
        <path d="M23 4v6h-6M1 20v-6h6" />
        <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15" />
    </svg>
);

/**
 * Edit Component for the AI Summary Generator Block
 *
 * @param {Object} props Block properties
 * @param {Object} props.attributes Block attributes
 * @param {Function} props.setAttributes Attribute setter
 * @param {Object} props.context Block context
 */
const Edit = ({ attributes, setAttributes, context }) => {
    const blockProps = useBlockProps({
        className: 'xebia-ai-summary-block-editor',
    });

    const [isLoading, setIsLoading] = useState(false);
    const [error, setError] = useState(null);
    const [traceability, setTraceability] = useState(null);

    // Get current post data
    const { postId, postContent, postTitle } = useSelect((select) => {
        const editor = select('core/editor');
        return {
            postId: editor?.getCurrentPostId() || context?.postId,
            postContent: editor?.getEditedPostContent() || '',
            postTitle: editor?.getEditedPostAttribute('title') || '',
        };
    }, [context]);

    /**
     * Generate AI summary for current post content
     */
    const generateSummary = useCallback(async () => {
        if (!postContent || postContent.trim().length === 0) {
            setError('Please add some content to the post before generating a summary.');
            return;
        }

        setIsLoading(true);
        setError(null);

        try {
            const response = await apiFetch({
                path: '/xebia-authority/v1/preview-summary',
                method: 'POST',
                data: {
                    content: postContent,
                    title: postTitle,
                },
            });

            if (response.success) {
                setAttributes({
                    summary: response.data.summary,
                    metadata: response.data.metadata,
                    lastGenerated: new Date().toISOString(),
                });
                setTraceability(response.data.traceability);
            } else {
                setError(response.error || 'Failed to generate summary');
                setTraceability(response.traceability);
            }
        } catch (err) {
            setError(err.message || 'An unexpected error occurred');
        } finally {
            setIsLoading(false);
        }
    }, [postContent, postTitle, setAttributes]);

    /**
     * Save summary to post meta via ACF
     */
    const saveSummary = useCallback(async () => {
        if (!attributes.summary || !postId) return;

        setIsLoading(true);
        try {
            await apiFetch({
                path: `/xebia-authority/v1/generate-summary/${postId}`,
                method: 'POST',
            });
            setError(null);
        } catch (err) {
            setError('Failed to save summary: ' + err.message);
        } finally {
            setIsLoading(false);
        }
    }, [attributes.summary, postId]);

    return (
        <div {...blockProps}>
            <div className="xebia-ai-summary-editor-container">
                {/* Header */}
                <div className="xebia-ai-summary-header">
                    <div className="xebia-ai-summary-header-left">
                        <AIIcon />
                        <h3>AI Executive Summary Generator</h3>
                    </div>
                    <span className="xebia-ai-badge">Powered by GPT-4o</span>
                </div>

                {/* Error Notice */}
                {error && (
                    <Notice status="error" isDismissible onRemove={() => setError(null)}>
                        {error}
                    </Notice>
                )}

                {/* Main Content Area */}
                <div className="xebia-ai-summary-content">
                    {isLoading ? (
                        <div className="xebia-ai-loading-state">
                            <Spinner />
                            <p>Generating executive summary with AI...</p>
                            <small>This may take a few moments</small>
                        </div>
                    ) : attributes.summary ? (
                        <div className="xebia-ai-summary-result">
                            <div className="xebia-ai-summary-text">
                                <p>{attributes.summary}</p>
                            </div>
                            {attributes.metadata && (
                                <div className="xebia-ai-summary-meta">
                                    <span>Model: {attributes.metadata.model}</span>
                                    <span>Tokens: {attributes.metadata.usage?.total_tokens || 'N/A'}</span>
                                    <span>Generated: {new Date(attributes.lastGenerated).toLocaleString()}</span>
                                </div>
                            )}
                        </div>
                    ) : (
                        <div className="xebia-ai-empty-state">
                            <AIIcon />
                            <p>Click "Generate with AI" to create an executive summary of your content.</p>
                            <small>The summary will be generated using OpenAI's GPT-4o model.</small>
                        </div>
                    )}
                </div>

                {/* Actions */}
                <div className="xebia-ai-summary-actions">
                    <Button
                        variant="primary"
                        onClick={generateSummary}
                        disabled={isLoading}
                        className="xebia-ai-generate-btn"
                    >
                        {isLoading ? (
                            <>
                                <Spinner />
                                Generating...
                            </>
                        ) : (
                            <>
                                <RegenerateIcon />
                                {attributes.summary ? 'Regenerate' : 'Generate with AI'}
                            </>
                        )}
                    </Button>

                    {attributes.summary && (
                        <Button
                            variant="secondary"
                            onClick={() => setAttributes({ showTraceability: !attributes.showTraceability })}
                        >
                            {attributes.showTraceability ? 'Hide' : 'Show'} Traceability
                        </Button>
                    )}
                </div>

                {/* Traceability Panel */}
                {attributes.showTraceability && traceability && (
                    <Panel className="xebia-ai-traceability-panel">
                        <PanelBody title="AI Traceability Data" initialOpen={true}>
                            <div className="xebia-ai-traceability-content">
                                <div className="xebia-ai-trace-section">
                                    <h4>Request Payload</h4>
                                    <pre>{JSON.stringify(JSON.parse(traceability.requestPayload || '{}'), null, 2)}</pre>
                                </div>
                                <div className="xebia-ai-trace-section">
                                    <h4>Response Data</h4>
                                    <pre>{JSON.stringify(JSON.parse(traceability.responseData || '{}'), null, 2)}</pre>
                                </div>
                                <div className="xebia-ai-trace-section">
                                    <h4>Performance</h4>
                                    <p>Duration: {traceability.durationMs?.toFixed(2)}ms</p>
                                    <p>Timestamp: {traceability.timestamp}</p>
                                </div>
                            </div>
                        </PanelBody>
                    </Panel>
                )}
            </div>
        </div>
    );
};

/**
 * Save Component - Dynamic block renders via PHP
 */
const Save = () => null;

/**
 * Register the block type
 */
registerBlockType(metadata.name, {
    icon: <AIIcon />,
    edit: Edit,
    save: Save,
});
