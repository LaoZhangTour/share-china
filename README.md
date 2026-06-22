Share China
![Latest Stable Version](https://img.shields.io/packagist/v/xiabibi/share-china.svg)
![Total Downloads](https://img.shields.io/packagist/dt/xiabibi/share-china.svg)
![License](https://img.shields.io/packagist/l/xiabibi/share-china.svg)
一款极简的 Flarum 微信分享插件，为帖子生成专属微信画布分享卡片，让社区内容在微信生态中获得更优雅的传播体验。
🔗 演示站点：https://www.xiabibi.top
✨ 功能特性
🎨 微信画布分享：自动生成符合微信规范的分享卡片，告别丑陋的纯链接转发
⚡️ 极致轻量：前端构建产物仅几 KB，零额外依赖，不影响页面加载速度
🔒 隐私友好：不收集任何用户数据，分享链接不含追踪参数
📱 移动端适配：自动识别微信环境，无缝衔接分享流程
🚀 开箱即用：无需任何后台配置，启用即可生效
📦 安装
方式一：Composer 安装（推荐）
bash

编辑



composer require xiabibi/share-china
php flarum migrate
php flarum cache:clear
方式二：手动安装
下载 ZIP 包并解压到 extensions/share-china 目录
进入插件目录安装 PHP 依赖：
bash

编辑



cd extensions/share-china
composer install --no-dev
在 Flarum 后台 → 扩展程序 → 启用「Share China」
清除缓存：php flarum cache:clear
🛠️ 开发 & 构建
如需修改前端源码并重新构建：
bash

编辑



cd js
npm install
npm run build   # 生产构建，产物输出到 dist/
npm run dev     # 开发模式，监听文件变化自动构建
⚠️ 注意：node_modules/ 仅开发时需要，分发打包时请勿包含此目录。
📄 许可证
MIT License © 2024 xiabibi
🤝 反馈与交流
欢迎提交 Issue 和 Pull Request！也欢迎访问 https://www.xiabibi.top 交流使用体验。
💡 补充提醒
Composer 包名：模板中暂用 xiabibi/share-china，请替换为你实际注册的 Packagist 包名。
版权年份：许可证处的 2024 请改为插件首次发布的实际年份。
功能描述校准：如果“微信画布分享”有特定的触发方式（例如点击某个按钮、长按图片等），可以在「功能特性」下方补充一行简短的使用说明，方便新用户快速上手。
