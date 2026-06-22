<?php

use Flarum\Extend;
use Flarum\Frontend\Document;

return [
    (new Extend\Frontend('forum'))
        ->css(__DIR__ . '/less/forum.less'),

    (new Extend\Frontend('forum'))
        ->content(function (Document $document) {
            $document->head[] = '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">';

            $scriptAndStyle = <<<'HTML'
<style>
.share-china-inline { display: flex; justify-content: flex-end; align-items: center; margin-top: 16px; padding-top: 12px; border-top: 1px dashed #e9ecef; }
.share-china-list { display: flex; gap: 10px; align-items: center; }
.share-china-btn { display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 50%; background: #f8f9fa; border: 1px solid #dee2e6; color: #6c757d; font-size: 14px; text-decoration: none; cursor: pointer; transition: all 0.2s ease; padding: 0; line-height: 1; }
.share-china-btn:hover { background: #fff; transform: translateY(-2px); box-shadow: 0 2px 6px rgba(0,0,0,0.08); }
.share-china-btn.weibo { color: #e6162d; }
.share-china-btn.qq { color: #12b7f5; }
.share-china-btn.qzone { color: #ffcc00; }
.share-china-btn.wechat { color: #07c160; }
.share-china-btn i, .share-china-btn svg { font-size: 16px !important; width: 16px !important; height: 16px !important; line-height: 1 !important; }

#share-poster-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); display: flex; align-items: center; justify-content: center; z-index: 9999; opacity: 0; transition: opacity 0.3s; }
#share-poster-modal { background: #fff; border-radius: 12px; padding: 24px; max-width: 320px; width: 90%; box-shadow: 0 10px 30px rgba(0,0,0,0.2); text-align: center; transform: scale(0.9); transition: transform 0.3s ease; }
#share-poster-modal.active { transform: scale(1); }
#share-poster-modal h3 { margin: 0 0 16px 0; color: #333; font-size: 16px; font-weight: 600; }
#poster-canvas-container { margin: 0 auto 16px; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
#poster-canvas-container canvas { display: block; width: 100%; height: auto; }
.poster-actions { display: flex; gap: 10px; justify-content: center; }
.poster-actions button { padding: 8px 20px; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; transition: opacity 0.2s; }
.poster-actions button:hover { opacity: 0.85; }
.btn-download { background: #07c160; color: #fff; }
.btn-close { background: #f1f3f5; color: #495057; }
</style>

<script>
(function() {
    function loadQRCode() {
        if (window.QRCode) return Promise.resolve();
        return new Promise((resolve) => {
            const s = document.createElement('script');
            s.src = 'https://cdn.jsdelivr.net/npm/qrcode-generator@1.4.4/qrcode.min.js';
            s.onload = () => resolve();
            document.head.appendChild(s);
        });
    }

    // ✅ 获取纯净标题（去除站点名称后缀）
    function getCleanTitle() {
        var title = document.title || '';
        // Flarum 默认格式: "帖子标题 - 站点名称"
        var separator = ' - ';
        var idx = title.lastIndexOf(separator);
        if (idx > 0) {
            title = title.substring(0, idx).trim();
        }
        return title || '精彩讨论';
    }

    // ✅ 获取 Favicon URL
    function getFaviconUrl() {
        var link = document.querySelector("link[rel~='icon']") || document.querySelector("link[rel='shortcut icon']");
        return link ? link.href : '/favicon.ico';
    }

    // ✅ 智能摘要提取（纯文本 + 清理）
    function getPostSummary() {
        var postBody = document.querySelector('.PostStream-item:first-child .Post-body');
        if (!postBody) return '查看精彩讨论...';
        var text = (postBody.innerText || postBody.textContent || '').replace(/\s+/g, ' ').trim();
        return text.length > 120 ? text.substring(0, 120) + '...' : text;
    }

    function injectShareButtons() {
        var postBody = document.querySelector('.PostStream-item:first-child .Post-body');
        if (!postBody || postBody.querySelector('.share-china-inline')) return;

        var url = encodeURIComponent(window.location.href);
        var title = encodeURIComponent(document.title);
        var html = '<div class="share-china-inline"><div class="share-china-list">' +
            '<a href="https://service.weibo.com/share/share.php?url=' + url + '&title=' + title + '" target="_blank" rel="noopener noreferrer" class="share-china-btn weibo" title="微博"><i class="fab fa-weibo"></i></a>' +
            '<a href="https://connect.qq.com/widget/shareqq/index.html?url=' + url + '&title=' + title + '" target="_blank" rel="noopener noreferrer" class="share-china-btn qq" title="QQ"><i class="fab fa-qq"></i></a>' +
            '<a href="https://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?url=' + url + '&title=' + title + '" target="_blank" rel="noopener noreferrer" class="share-china-btn qzone" title="QQ空间"><i class="fas fa-star"></i></a>' +
            '<button type="button" class="share-china-btn wechat" title="生成分享海报"><i class="fab fa-weixin"></i></button>' +
            '</div></div>';
        postBody.insertAdjacentHTML('beforeend', html);

        var wechatBtn = postBody.querySelector('.share-china-btn.wechat');
        if (wechatBtn) {
            wechatBtn.addEventListener('click', function(e) {
                e.preventDefault();
                showPosterModal();
            });
        }
    }

    function showPosterModal() {
        if (document.getElementById('share-poster-overlay')) return;
        var overlay = document.createElement('div');
        overlay.id = 'share-poster-overlay';
        overlay.innerHTML = '<div id="share-poster-modal">' +
            '<h3>保存海报分享</h3>' +
            '<div id="poster-canvas-container"><canvas id="share-poster-canvas"></canvas></div>' +
            '<div class="poster-actions">' +
            '<button class="btn-download" onclick="downloadPoster()">下载海报</button>' +
            '<button class="btn-close" onclick="closePosterModal()">关闭</button>' +
            '</div></div>';
        overlay.addEventListener('click', function(e) { if (e.target === overlay) closePosterModal(); });
        document.body.appendChild(overlay);
        requestAnimationFrame(() => {
            overlay.style.opacity = '1';
            document.getElementById('share-poster-modal').classList.add('active');
            generatePoster();
        });
    }

    // ✅ 核心：Canvas 海报生成（重构版）
    function generatePoster() {
        var canvas = document.getElementById('share-poster-canvas');
        var ctx = canvas.getContext('2d');
        var dpr = window.devicePixelRatio || 1;
        var cssWidth = 280;
        
        // 先设置 CSS 显示尺寸
        canvas.style.width = cssWidth + 'px';
        
        // 预计算内容高度（避免裁切）
        var padding = 24;
        var titleFontSize = 17;
        var summaryFontSize = 13;
        var lineHeight = 20;
        var qrSize = 90;
        var bottomArea = qrSize + 40; // 二维码 + 底部提示文字 + 间距
        
        // 模拟换行计算实际摘要行数
        ctx.font = summaryFontSize + 'px "PingFang SC", "Microsoft YaHei", sans-serif';
        var summary = getPostSummary();
        var maxTextWidth = cssWidth - padding * 2;
        var summaryLines = wrapTextChinese(ctx, summary, maxTextWidth);
        var maxSummaryLines = 8; // 最多8行摘要
        if (summaryLines.length > maxSummaryLines) {
            summaryLines = summaryLines.slice(0, maxSummaryLines);
            // 修正最后一行加省略号
            var lastLine = summaryLines[summaryLines.length - 1];
            while (ctx.measureText(lastLine + '...').width > maxTextWidth && lastLine.length > 0) {
                lastLine = lastLine.slice(0, -1);
            }
            summaryLines[summaryLines.length - 1] = lastLine + '...';
        }
        
        var contentHeight = padding + titleFontSize + 12 + (summaryLines.length * lineHeight) + 20 + bottomArea + padding;
        
        // 设置高清 Canvas 实际像素
        canvas.width = cssWidth * dpr;
        canvas.height = contentHeight * dpr;
        canvas.style.height = contentHeight + 'px';
        ctx.scale(dpr, dpr);

        // 1. 白色背景
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, cssWidth, contentHeight);

        // 2. ✅ Favicon 水印（异步加载后绘制）
        var faviconUrl = getFaviconUrl();
        var watermarkImg = new Image();
        watermarkImg.crossOrigin = 'anonymous';
        watermarkImg.onload = function() {
            var wmSize = 120;
            var wmX = cssWidth - wmSize - 10;
            var wmY = 10;
            ctx.save();
            ctx.globalAlpha = 0.06;
            ctx.drawImage(watermarkImg, wmX, wmY, wmSize, wmSize);
            ctx.restore();
        };
        watermarkImg.onerror = function() { /* Favicon 加载失败则跳过水印 */ };
        watermarkImg.src = faviconUrl;

        // 3. ✅ 纯净标题
        var title = getCleanTitle();
        ctx.font = 'bold ' + titleFontSize + 'px "PingFang SC", "Microsoft YaHei", sans-serif';
        ctx.fillStyle = '#1a1a1a';
        ctx.textAlign = 'left';
        ctx.textBaseline = 'top';
        var titleY = padding;
        // 标题也做截断保护
        var displayTitle = title;
        while (ctx.measureText(displayTitle + '...').width > maxTextWidth && displayTitle.length > 0) {
            displayTitle = displayTitle.slice(0, -1);
        }
        if (displayTitle !== title) displayTitle += '...';
        ctx.fillText(displayTitle, padding, titleY);

        // 4. 分割线
        var lineY = titleY + titleFontSize + 10;
        ctx.strokeStyle = '#f0f0f0';
        ctx.lineWidth = 1;
        ctx.beginPath();
        ctx.moveTo(padding, lineY);
        ctx.lineTo(cssWidth - padding, lineY);
        ctx.stroke();

        // 5. ✅ 摘要（已精确计算行数，不会裁切）
        ctx.font = summaryFontSize + 'px "PingFang SC", "Microsoft YaHei", sans-serif';
        ctx.fillStyle = '#555555';
        var summaryStartY = lineY + 12;
        for (var i = 0; i < summaryLines.length; i++) {
            ctx.fillText(summaryLines[i], padding, summaryStartY + i * lineHeight);
        }

        // 6. ✅ 二维码（缩小 + 底部锚定）
        loadQRCode().then(function() {
            var qr = qrcode(0, 'M');
            qr.addData(window.location.href);
            qr.make();
            
            var qrX = (cssWidth - qrSize) / 2;
            var qrY = contentHeight - padding - qrSize - 20; // 底部锚定
            
            // 二维码浅色底板
            ctx.fillStyle = '#f8f9fa';
            var qrPad = 8;
            ctx.beginPath();
            ctx.roundRect(qrX - qrPad, qrY - qrPad, qrSize + qrPad * 2, qrSize + qrPad * 2, 6);
            ctx.fill();
            
            // 绘制二维码模块
            ctx.fillStyle = '#333333';
            var moduleCount = qr.getModuleCount();
            var cellSize = qrSize / moduleCount;
            for (var row = 0; row < moduleCount; row++) {
                for (var col = 0; col < moduleCount; col++) {
                    if (qr.isDark(row, col)) {
                        ctx.fillRect(qrX + col * cellSize, qrY + row * cellSize, cellSize, cellSize);
                    }
                }
            }
            
            // 底部提示
            ctx.font = '11px "PingFang SC", "Microsoft YaHei", sans-serif';
            ctx.fillStyle = '#aaaaaa';
            ctx.textAlign = 'center';
            ctx.fillText('长按识别二维码 · 查看完整内容', cssWidth / 2, contentHeight - padding + 2);
        });
    }

    // ✅ 中文友好换行算法（逐字符测量，完美避免裁切）
    function wrapTextChinese(ctx, text, maxWidth) {
        var lines = [];
        var currentLine = '';
        for (var i = 0; i < text.length; i++) {
            var ch = text[i];
            var testLine = currentLine + ch;
            if (ctx.measureText(testLine).width > maxWidth && currentLine.length > 0) {
                lines.push(currentLine);
                currentLine = ch;
            } else {
                currentLine = testLine;
            }
        }
        if (currentLine) lines.push(currentLine);
        return lines;
    }

        window.downloadPoster = function() {
        var canvas = document.getElementById('share-poster-canvas');
        if (!canvas) return;
        try {
            canvas.toBlob(function(blob) {
                if (!blob) {
                    // toBlob 失败时降级
                    var dataUrl = canvas.toDataURL('image/png');
                    var link = document.createElement('a');
                    link.download = 'xiabibi-share.png';
                    link.href = dataUrl;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    return;
                }
                var url = URL.createObjectURL(blob);
                var link = document.createElement('a');
                link.download = 'xiabibi-share.png';
                link.href = url;
                document.body.appendChild(link);
                link.click();
                setTimeout(function() {
                    document.body.removeChild(link);
                    URL.revokeObjectURL(url);
                }, 100);
            }, 'image/png');
        } catch (e) {
            console.error('Canvas download failed:', e);
            alert('海报生成失败，请长按海报图片手动保存');
        }
    };

    window.closePosterModal = function() {
        var o = document.getElementById('share-poster-overlay');
        if (!o) return;
        o.style.opacity = '0';
        var modal = o.querySelector('#share-poster-modal');
        if (modal) modal.classList.remove('active');
        setTimeout(function() { o.remove(); }, 300);
    };

    // SPA 注入策略
    var injectTimer = null;
    function safeInject() {
        clearTimeout(injectTimer);
        injectTimer = setTimeout(function() {
            var postBody = document.querySelector('.PostStream-item:first-child .Post-body');
            if (postBody && !postBody.querySelector('.share-china-inline')) {
                injectShareButtons();
            }
        }, 400);
    }
    if (typeof app !== 'undefined' && app.router) {
        app.router.on('did-resolve', function() { setTimeout(injectShareButtons, 400); });
    }
    document.addEventListener('DOMContentLoaded', function() { setTimeout(injectShareButtons, 500); });
    var obs = new MutationObserver(function(mutations) {
        var hasDomChange = mutations.some(function(m) { return m.addedNodes.length > 0 || m.removedNodes.length > 0; });
        if (hasDomChange) safeInject();
    });
    obs.observe(document.body, { childList: true, subtree: true });
})();
</script>
HTML;

            $document->foot[] = $scriptAndStyle;
        }),
];