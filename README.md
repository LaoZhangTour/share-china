# Share China

[![Latest Stable Version](https://img.shields.io/packagist/v/xiabibi/share-china.svg)](https://packagist.org/packages/xiabibi/share-china)
[![Total Downloads](https://img.shields.io/packagist/dt/xiabibi/share-china.svg)](https://packagist.org/packages/xiabibi/share-china)
[![License](https://img.shields.io/packagist/l/xiabibi/share-china.svg)](https://packagist.org/packages/xiabibi/share-china)

一款极简的 Flarum 微信分享插件，为帖子生成专属微信画布分享卡片，让社区内容在微信生态中获得更优雅的传播体验。

> 🔗 **演示站点**：[https://www.xiabibi.top](https://www.xiabibi.top)

## ✨ 功能特性

- 🎨 **微信画布分享**：自动生成符合微信规范的分享卡片，告别丑陋的纯链接转发
- ⚡️ **极致轻量**：前端构建产物仅几 KB，零额外依赖，不影响页面加载速度
- 🔒 **隐私友好**：不收集任何用户数据，分享链接不含追踪参数
- 📱 **移动端适配**：自动识别微信环境，无缝衔接分享流程
- 🚀 **开箱即用**：无需任何后台配置，启用即可生效

## 📦 安装

### 方式一：Composer 安装（推荐）

```bash
composer require xiabibi/share-china
php flarum migrate
php flarum cache:clear
