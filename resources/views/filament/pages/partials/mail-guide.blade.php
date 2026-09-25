<div class="guide-grid-2">
    {{-- Card 1: Domain Mail --}}
    <div class="guide-card">
        <div style="display: flex; flex-direction: column; gap: 0.875rem;">
            <div style="display: flex; align-items: center; gap: 0.625rem;">
                <span style="padding: 0.25rem 0.625rem; border-radius: 0.5rem; background-color: #fef3c7; color: #92400e; font-weight: 700; font-size: 0.75rem; display: inline-flex; align-items: center;">
                    🌐 Cách 1
                </span>
                <h5 class="guide-title">
                    Khi Đã Mua Domain Riêng (Ví dụ: linhmakeup.vn)
                </h5>
            </div>

            <ul class="guide-list">
                <li class="guide-list-item">
                    <span class="guide-dot guide-dot-amber">•</span>
                    <span><strong>Máy chủ (Host):</strong> Thường là <code class="guide-code">mail.linhmakeup.vn</code> hoặc máy chủ của nhà cung cấp hosting (cPanel, DirectAdmin, Google Workspace, Zoho Mail, v.v.).</span>
                </li>
                <li class="guide-list-item">
                    <span class="guide-dot guide-dot-amber">•</span>
                    <span><strong>Cổng kết nối (Port):</strong> Chọn <code class="guide-code">465</code> (kèm mã hóa <strong>SSL</strong>) hoặc <code class="guide-code">587</code> (kèm mã hóa <strong>TLS</strong>).</span>
                </li>
                <li class="guide-list-item">
                    <span class="guide-dot guide-dot-amber">•</span>
                    <span><strong>From Address:</strong> Nên đặt là <code class="guide-code">booking@linhmakeup.vn</code> hoặc <code class="guide-code">contact@linhmakeup.vn</code> để tăng uy tín thương hiệu và tránh rơi vào Spam.</span>
                </li>
                <li class="guide-list-item">
                    <span class="guide-dot guide-dot-amber">•</span>
                    <span><strong>Bản ghi DNS:</strong> Hãy đảm bảo bản ghi <code class="guide-code">SPF</code> và <code class="guide-code">DKIM</code> trong trang quản trị DNS tên miền đã được trỏ về máy chủ mail của bạn.</span>
                </li>
            </ul>
        </div>

        <div class="guide-card-footer guide-footer-amber">
            💡 Gợi ý: Bấm nút preset <strong>"🌐 Email Tên Miền (SSL 465)"</strong> ở trên để tự động điền nhanh các thông số.
        </div>
    </div>

    {{-- Card 2: Gmail --}}
    <div class="guide-card">
        <div style="display: flex; flex-direction: column; gap: 0.875rem;">
            <div style="display: flex; align-items: center; gap: 0.625rem;">
                <span style="padding: 0.25rem 0.625rem; border-radius: 0.5rem; background-color: #ffe4e6; color: #9f1239; font-weight: 700; font-size: 0.75rem; display: inline-flex; align-items: center;">
                    🔴 Cách 2
                </span>
                <h5 class="guide-title">
                    Khi Dùng Tài Khoản Gmail Cá Nhân (@gmail.com)
                </h5>
            </div>

            <ul class="guide-list">
                <li class="guide-list-item">
                    <span class="guide-dot guide-dot-rose">•</span>
                    <span><strong>Máy chủ (Host):</strong> Điền chuẩn <code class="guide-code">smtp.gmail.com</code> | Cổng: <code class="guide-code">587</code> | Mã hóa: <code class="guide-code">TLS</code>.</span>
                </li>
                <li class="guide-list-item">
                    <span class="guide-dot guide-dot-rose">•</span>
                    <span><strong>Tài khoản (Username):</strong> Điền đầy đủ email Gmail của bạn (vd: <code class="guide-code">linhmakeup@gmail.com</code>).</span>
                </li>
                <li class="guide-list-item" style="flex-direction: column; gap: 0.375rem;">
                    <div style="display: flex; align-items: flex-start; gap: 0.5rem;">
                        <span class="guide-dot guide-dot-rose">•</span>
                        <span><strong>Mật khẩu (Password):</strong> <span style="color: #e11d48; font-weight: 700;">BẮT BUỘC</span> dùng <em>Mật khẩu ứng dụng (App Password)</em> 16 ký tự do Google cấp (không phải mật khẩu đăng nhập thông thường).</span>
                    </div>
                    <div class="guide-sub-box" style="margin-left: 1rem;">
                        👉 Cách lấy: Vào <strong>Google Account</strong> &gt; <strong>Bảo mật (Security)</strong> &gt; <strong>Xác minh 2 bước</strong> &gt; <strong>Mật khẩu ứng dụng</strong> &gt; Tạo mật khẩu tên "Website Makeup" rồi dán 16 ký tự vào ô Mật khẩu.
                    </div>
                </li>
            </ul>
        </div>

        <div class="guide-card-footer guide-footer-rose">
            💡 Gợi ý: Bấm nút preset <strong>"🔴 Gmail (App Password)"</strong> để tự động điền máy chủ và cổng kết nối.
        </div>
    </div>
</div>
