import { extend } from 'flarum/common/extend';
import DiscussionPage from 'flarum/forum/components/DiscussionPage';
import Button from 'flarum/common/components/Button';

app.initializers.add('fof-share-china', () => {
  extend(DiscussionPage.prototype, 'sidebarItems', function (items) {
    const discussion = this.discussion;
    if (!discussion) return;

    const url = encodeURIComponent(app.forum.attribute('baseUrl') + '/d/' + discussion.id() + '-' + discussion.slug());
    const title = encodeURIComponent(discussion.title());

    items.add('share-china', m('.ShareChina', [
      m(Button, {
        className: 'Button ShareWechat',
        icon: 'fab fa-weixin',
        onclick: () => alert('请手动复制链接分享：\n' + decodeURIComponent(url)),
      }, '微信'),
      m(Button, {
        className: 'Button ShareWeibo',
        icon: 'fab fa-weibo',
        onclick: () => window.open(`https://service.weibo.com/share/share.php?url=${url}&title=${title}`, '_blank'),
      }, '微博'),
      m(Button, {
        className: 'Button ShareQQ',
        icon: 'fab fa-qq',
        onclick: () => window.open(`https://connect.qq.com/widget/shareqq/index.html?url=${url}&title=${title}`, '_blank'),
      }, 'QQ'),
    ]), 10);
  });
});
