const config = require('flarum-webpack-config')();

// 强制覆盖自动检测的入口点
config.entry = {
    forum: './src/forum/index.js'
};

module.exports = config;
