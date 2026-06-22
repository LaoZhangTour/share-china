import app from 'flarum/forum/app';
import { extend } from 'flarum/common/extend';
import DiscussionPage from 'flarum/forum/components/DiscussionPage';
import ShareButtons from './components/ShareButtons';

app.initializers.add('fof/share-china', () => {
    extend(DiscussionPage.prototype, 'sidebarItems', function (items) {
        items.add('share-china', <ShareButtons discussion={this.discussion} />, 10);
    });
});
