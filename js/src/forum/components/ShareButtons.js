import Component from 'flarum/common/Component';
import app from 'flarum/forum/app';

export default class ShareButtons extends Component {
    oninit(vnode) {
        super.oninit(vnode);
        this.discussion = vnode.attrs.discussion;
    }

    view() {
        const url = encodeURIComponent(window.location.href);
        const title = encodeURIComponent(this.discussion.title());

        return (
            <div className="ShareChina-buttons">
                <h4>{app.translator.trans('fof-share-china.forum.share_title')}</h4>
                <div className="ShareChina-list">
                    <a href={`https://service.weibo.com/share/share.php?url=${url}&title=${title}`} target="_blank" rel="noopener noreferrer" className="ShareChina-btn weibo" title="微博">
                        <i className="fab fa-weibo"></i>
                    </a>
                    <a href={`https://connect.qq.com/widget/shareqq/index.html?url=${url}&title=${title}`} target="_blank" rel="noopener noreferrer" className="ShareChina-btn qq" title="QQ">
                        <i className="fab fa-qq"></i>
                    </a>
                    <a href={`https://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?url=${url}&title=${title}`} target="_blank" rel="noopener noreferrer" className="ShareChina-btn qzone" title="QQ空间">
                        <i className="fas fa-star"></i>
                    </a>
                    <button className="ShareChina-btn wechat" onclick={() => alert('请使用手机微信扫描二维码分享')} title="微信">
                        <i className="fab fa-weixin"></i>
                    </button>
                </div>
            </div>
        );
    }
}
