import './bootstrap';
import 'quill/dist/quill.snow.css';
import 'select2/dist/css/select2.css';

import Alpine from 'alpinejs';
import { Chart, registerables } from 'chart.js';
import jQuery from 'jquery';
import Quill from 'quill';
import select2 from 'select2';

Chart.register(...registerables);

window.$ = window.jQuery = jQuery;
select2(window, jQuery);

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('form').forEach((form) => {
        const statusSelect = form.querySelector('[data-status-selector]');
        const publishDateInput = form.querySelector('[data-publish-date-input]');
        const publishDateHelper = form.querySelector('[data-publish-date-helper]');

        if (! statusSelect || ! publishDateInput) {
            return;
        }

        const currentDateTimeLocal = () => {
            const now = new Date();
            now.setSeconds(0, 0);

            const timezoneOffset = now.getTimezoneOffset() * 60000;

            return new Date(now.getTime() - timezoneOffset).toISOString().slice(0, 16);
        };

        const syncPublishDateState = () => {
            if (statusSelect.value === 'scheduled') {
                publishDateInput.min = currentDateTimeLocal();

                if (publishDateHelper) {
                    publishDateHelper.textContent = 'Scheduled posts must use a future publish date.';
                }

                return;
            }

            publishDateInput.removeAttribute('min');

            if (publishDateHelper) {
                publishDateHelper.textContent = 'You can set the publish date to now or any past date for published posts.';
            }
        };

        statusSelect.addEventListener('change', syncPublishDateState);
        syncPublishDateState();
    });

    $('[data-select2-tags]').select2({
        tags: true,
        tokenSeparators: [','],
        width: '100%',
        placeholder: 'Search or create tags',
        createTag(params) {
            const term = $.trim(params.term);

            if (term === '') {
                return null;
            }

            return {
                id: `__new__:${term}`,
                text: term,
                newTag: true,
            };
        },
    });

    document.querySelectorAll('[data-quill-editor]').forEach((editor) => {
        const input = document.querySelector(editor.dataset.quillInput);

        if (! input) {
            return;
        }

        const quill = new Quill(editor, {
            modules: {
                toolbar: [
                    [{ header: [2, 3, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ list: 'ordered' }, { list: 'bullet' }],
                    ['blockquote', 'code-block'],
                    ['link'],
                    ['clean'],
                ],
            },
            placeholder: 'Write the post body...',
            theme: 'snow',
        });

        quill.root.innerHTML = input.value || '';

        const syncBody = () => {
            input.value = quill.root.innerHTML;
        };

        quill.on('text-change', syncBody);
        editor.closest('form')?.addEventListener('submit', syncBody);
    });

    document.querySelectorAll('[data-line-chart]').forEach((canvas) => {
        const rawData = canvas.dataset.chart;

        if (! rawData) {
            return;
        }

        const chartData = JSON.parse(rawData);

        new Chart(canvas, {
            type: 'line',
            data: chartData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            boxHeight: 12,
                            usePointStyle: true,
                        },
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                        },
                    },
                },
            },
        });
    });
});

window.Alpine = Alpine;

Alpine.start();
