import React from 'react';

import MenuItem from 'MenuItem.jsx';

class MenuItems extends React.Component {
	render () {
		return (
			{
				this.props.menuitems.map((menuitem) => (
					<MenuItem />
				));
			}
		);
	}
}

export default MenuItems;
