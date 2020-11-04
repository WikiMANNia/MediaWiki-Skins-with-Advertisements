import mustache from 'mustache';
import '../resources/skins.vector.styles/MenuDropdown.less';
import '../.storybook/common.less';
import { vectoradMenuTemplate, moreData, variantsData } from './MenuDropdown.stories.data';

export default {
	title: 'MenuDropdown'
};

export const more = () => mustache.render( vectoradMenuTemplate, moreData );

export const variants = () => mustache.render( vectoradMenuTemplate, variantsData );
