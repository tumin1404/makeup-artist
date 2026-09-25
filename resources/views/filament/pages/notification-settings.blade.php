<x-filament-panels::page>
    <div class="notification-settings-page space-y-6">
        <form wire:submit="save" class="space-y-6">
            {{ $this->form }}

            {{-- Footer Action Bar --}}
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-6 mt-6 border-t border-gray-200 dark:border-gray-800">
                <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                    <span class="inline-block w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Cấu hình có hiệu lực tức thì cho các lượt đặt lịch và email tiếp theo.</span>
                </div>

                <div class="flex items-center gap-3">
                    <x-filament::button 
                        type="submit" 
                        size="lg" 
                        color="primary" 
                        icon="heroicon-o-check"
                        wire:target="save"
                    >
                        <span wire:loading.remove wire:target="save">Lưu Toàn Bộ Cấu Hình</span>
                        <span wire:loading wire:target="save">Đang lưu dữ liệu...</span>
                    </x-filament::button>
                </div>
            </div>
        </form>
    </div>

    <style>
        /* =========================================================
           TABS STYLING - MODERN PILL SELECTOR
           ========================================================= */
        .notification-settings-page .fi-tabs {
            display: flex !important;
            flex-wrap: wrap !important;
            gap: 0.75rem !important;
            background-color: #f1f5f9 !important;
            border: 1px solid #e2e8f0 !important;
            padding: 0.5rem !important;
            border-radius: 1rem !important;
            margin-bottom: 1.5rem !important;
        }
        :is(.dark) .notification-settings-page .fi-tabs,
        html.dark .notification-settings-page .fi-tabs,
        body.dark .notification-settings-page .fi-tabs {
            background-color: #0f172a !important;
            border-color: #334155 !important;
        }

        /* Tab Item Pill */
        .notification-settings-page .fi-tabs-item {
            display: inline-flex !important;
            align-items: center !important;
            gap: 0.625rem !important;
            padding: 0.625rem 1.25rem !important;
            border-radius: 0.75rem !important;
            font-size: 0.875rem !important;
            font-weight: 600 !important;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
            color: #64748b !important;
            background-color: transparent !important;
            border: 1.5px solid transparent !important;
            cursor: pointer !important;
            box-shadow: none !important;
            margin: 0 !important;
        }
        :is(.dark) .notification-settings-page .fi-tabs-item,
        html.dark .notification-settings-page .fi-tabs-item,
        body.dark .notification-settings-page .fi-tabs-item {
            color: #94a3b8 !important;
        }

        /* Hover state for inactive tabs */
        .notification-settings-page .fi-tabs-item:not(.fi-active):not([aria-selected="true"]):hover {
            background-color: rgba(255, 255, 255, 0.7) !important;
            color: #1e293b !important;
            border-color: #cbd5e1 !important;
        }
        :is(.dark) .notification-settings-page .fi-tabs-item:not(.fi-active):not([aria-selected="true"]):hover,
        html.dark .notification-settings-page .fi-tabs-item:not(.fi-active):not([aria-selected="true"]):hover,
        body.dark .notification-settings-page .fi-tabs-item:not(.fi-active):not([aria-selected="true"]):hover {
            background-color: rgba(30, 41, 59, 0.7) !important;
            color: #f8fafc !important;
            border-color: #475569 !important;
        }

        /* Active Tab State - HIGH CONTRAST AMBER ACCENT */
        .notification-settings-page .fi-tabs-item.fi-active,
        .notification-settings-page .fi-tabs-item[aria-selected="true"] {
            background-color: #ffffff !important;
            color: #b45309 !important;
            border-color: #f59e0b !important;
            box-shadow: 0 4px 14px -2px rgba(245, 158, 11, 0.25), 0 2px 4px -2px rgba(0, 0, 0, 0.05) !important;
        }
        :is(.dark) .notification-settings-page .fi-tabs-item.fi-active,
        :is(.dark) .notification-settings-page .fi-tabs-item[aria-selected="true"],
        html.dark .notification-settings-page .fi-tabs-item.fi-active,
        html.dark .notification-settings-page .fi-tabs-item[aria-selected="true"],
        body.dark .notification-settings-page .fi-tabs-item.fi-active,
        body.dark .notification-settings-page .fi-tabs-item[aria-selected="true"] {
            background-color: #1e293b !important;
            color: #fbbf24 !important;
            border-color: #f59e0b !important;
            box-shadow: 0 4px 16px -2px rgba(245, 158, 11, 0.35) !important;
        }

        /* Active Tab Icon */
        .notification-settings-page .fi-tabs-item.fi-active .fi-tabs-item-icon,
        .notification-settings-page .fi-tabs-item[aria-selected="true"] .fi-tabs-item-icon {
            color: #d97706 !important;
        }
        :is(.dark) .notification-settings-page .fi-tabs-item.fi-active .fi-tabs-item-icon,
        :is(.dark) .notification-settings-page .fi-tabs-item[aria-selected="true"] .fi-tabs-item-icon,
        html.dark .notification-settings-page .fi-tabs-item.fi-active .fi-tabs-item-icon,
        html.dark .notification-settings-page .fi-tabs-item[aria-selected="true"] .fi-tabs-item-icon,
        body.dark .notification-settings-page .fi-tabs-item.fi-active .fi-tabs-item-icon,
        body.dark .notification-settings-page .fi-tabs-item[aria-selected="true"] .fi-tabs-item-icon {
            color: #fbbf24 !important;
        }

        /* Active Tab Badge */
        .notification-settings-page .fi-tabs-item.fi-active .fi-badge,
        .notification-settings-page .fi-tabs-item[aria-selected="true"] .fi-badge {
            font-weight: 700 !important;
            border: 1px solid currentColor !important;
        }

        /* =========================================================
           GUIDE CARDS & GRIDS (INDEPENDENT OF TAILWIND PURGE)
           ========================================================= */
        .guide-grid-2 {
            display: grid !important;
            grid-template-columns: 1fr !important;
            gap: 1.5rem !important;
            width: 100% !important;
            margin: 0.5rem 0 !important;
        }
        @media (min-width: 768px) {
            .guide-grid-2 {
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            }
        }

        .guide-grid-3 {
            display: grid !important;
            grid-template-columns: 1fr !important;
            gap: 1.25rem !important;
            width: 100% !important;
            margin: 0.5rem 0 !important;
        }
        @media (min-width: 768px) {
            .guide-grid-3 {
                grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
            }
        }

        /* Guide Card Base */
        .guide-card {
            background-color: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 1rem !important;
            padding: 1.5rem !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: space-between !important;
            gap: 1.25rem !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04) !important;
            box-sizing: border-box !important;
        }
        :is(.dark) .guide-card,
        html.dark .guide-card,
        body.dark .guide-card {
            background-color: #1e293b !important;
            border-color: #334155 !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3) !important;
        }

        /* Blue Guide Card (Zalo) */
        .guide-card-blue {
            background-color: #eff6ff !important;
            border-color: #bfdbfe !important;
        }
        :is(.dark) .guide-card-blue,
        html.dark .guide-card-blue,
        body.dark .guide-card-blue {
            background-color: #0f172a !important;
            border-color: #1e3a8a !important;
        }

        /* Step Badge Number */
        .guide-step-number {
            width: 2.25rem !important;
            height: 2.25rem !important;
            border-radius: 0.625rem !important;
            font-weight: 700 !important;
            font-size: 0.875rem !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            flex-shrink: 0 !important;
        }
        .guide-step-1 {
            background-color: #e0f2fe !important;
            color: #0369a1 !important;
        }
        :is(.dark) .guide-step-1,
        html.dark .guide-step-1,
        body.dark .guide-step-1 {
            background-color: rgba(14, 165, 233, 0.25) !important;
            color: #38bdf8 !important;
            border: 1px solid rgba(14, 165, 233, 0.4) !important;
        }

        .guide-step-2 {
            background-color: #f3e8ff !important;
            color: #7e22ce !important;
        }
        :is(.dark) .guide-step-2,
        html.dark .guide-step-2,
        body.dark .guide-step-2 {
            background-color: rgba(168, 85, 247, 0.25) !important;
            color: #c084fc !important;
            border: 1px solid rgba(168, 85, 247, 0.4) !important;
        }

        .guide-step-3 {
            background-color: #dcfce7 !important;
            color: #15803d !important;
        }
        :is(.dark) .guide-step-3,
        html.dark .guide-step-3,
        body.dark .guide-step-3 {
            background-color: rgba(34, 197, 94, 0.25) !important;
            color: #4ade80 !important;
            border: 1px solid rgba(34, 197, 94, 0.4) !important;
        }

        /* Headings and Titles */
        .guide-title {
            font-size: 0.9375rem !important;
            font-weight: 700 !important;
            color: #0f172a !important;
            line-height: 1.4 !important;
            margin: 0 !important;
        }
        :is(.dark) .guide-title,
        html.dark .guide-title,
        body.dark .guide-title {
            color: #f8fafc !important;
        }

        /* Text and Lists */
        .guide-text {
            font-size: 0.8125rem !important;
            line-height: 1.65 !important;
            color: #334155 !important;
            margin: 0 !important;
        }
        :is(.dark) .guide-text,
        html.dark .guide-text,
        body.dark .guide-text {
            color: #cbd5e1 !important;
        }

        .guide-list {
            display: flex !important;
            flex-direction: column !important;
            gap: 0.75rem !important;
            margin: 0 !important;
            padding: 0 !important;
            list-style: none !important;
        }

        .guide-list-item {
            display: flex !important;
            align-items: flex-start !important;
            gap: 0.5rem !important;
            font-size: 0.8125rem !important;
            line-height: 1.65 !important;
            color: #334155 !important;
        }
        :is(.dark) .guide-list-item,
        html.dark .guide-list-item,
        body.dark .guide-list-item {
            color: #cbd5e1 !important;
        }

        .guide-dot {
            font-weight: 700 !important;
            flex-shrink: 0 !important;
        }
        .guide-dot-amber { color: #d97706 !important; }
        .guide-dot-rose { color: #e11d48 !important; }
        .guide-dot-blue { color: #2563eb !important; }

        /* Inline Code Snippets - HIGH VISIBILITY CONTRAST */
        .guide-code {
            display: inline-block !important;
            padding: 0.15rem 0.45rem !important;
            border-radius: 0.375rem !important;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace !important;
            font-size: 0.75rem !important;
            font-weight: 600 !important;
            background-color: #f1f5f9 !important;
            color: #b45309 !important;
            border: 1px solid #cbd5e1 !important;
            margin: 0 0.125rem !important;
            word-break: break-word !important;
        }
        :is(.dark) .guide-code,
        html.dark .guide-code,
        body.dark .guide-code {
            background-color: #0f172a !important;
            color: #fbbf24 !important;
            border-color: #475569 !important;
        }

        /* Callout Sub-boxes (like App Password helper) */
        .guide-sub-box {
            margin-top: 0.5rem !important;
            padding: 0.75rem 1rem !important;
            border-radius: 0.75rem !important;
            background-color: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            font-size: 0.75rem !important;
            line-height: 1.6 !important;
            color: #475569 !important;
        }
        :is(.dark) .guide-sub-box,
        html.dark .guide-sub-box,
        body.dark .guide-sub-box {
            background-color: #0f172a !important;
            border-color: #334155 !important;
            color: #94a3b8 !important;
        }

        /* Card Footers */
        .guide-card-footer {
            padding-top: 0.875rem !important;
            border-top: 1px solid #e2e8f0 !important;
            font-size: 0.75rem !important;
            line-height: 1.5 !important;
            font-weight: 600 !important;
        }
        :is(.dark) .guide-card-footer,
        html.dark .guide-card-footer,
        body.dark .guide-card-footer {
            border-top-color: #334155 !important;
        }

        .guide-footer-amber { color: #b45309 !important; }
        :is(.dark) .guide-footer-amber, html.dark .guide-footer-amber, body.dark .guide-footer-amber { color: #fbbf24 !important; }

        .guide-footer-rose { color: #be123c !important; }
        :is(.dark) .guide-footer-rose, html.dark .guide-footer-rose, body.dark .guide-footer-rose { color: #fda4af !important; }

        .guide-footer-sky { color: #0284c7 !important; }
        :is(.dark) .guide-footer-sky, html.dark .guide-footer-sky, body.dark .guide-footer-sky { color: #38bdf8 !important; }

        .guide-footer-purple { color: #7e22ce !important; }
        :is(.dark) .guide-footer-purple, html.dark .guide-footer-purple, body.dark .guide-footer-purple { color: #c084fc !important; }

        .guide-footer-emerald { color: #15803d !important; }
        :is(.dark) .guide-footer-emerald, html.dark .guide-footer-emerald, body.dark .guide-footer-emerald { color: #4ade80 !important; }

        /* Alert Tip Box */
        .guide-tip-box {
            padding: 1rem 1.25rem !important;
            border-radius: 0.75rem !important;
            background-color: #ecfdf5 !important;
            border: 1px solid #a7f3d0 !important;
            color: #065f46 !important;
            display: flex !important;
            align-items: flex-start !important;
            gap: 0.75rem !important;
        }
        :is(.dark) .guide-tip-box,
        html.dark .guide-tip-box,
        body.dark .guide-tip-box {
            background-color: rgba(6, 78, 59, 0.35) !important;
            border-color: rgba(16, 185, 129, 0.4) !important;
            color: #a7f3d0 !important;
        }
    </style>
</x-filament-panels::page>
