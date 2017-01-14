import React from 'react';

import Menuitem from './Menuitem.jsx';
import {Card, CardActions, CardHeader, CardText} from 'material-ui/Card';
import FloatingActionButton from 'material-ui/FloatingActionButton';
import Chip from 'material-ui/Chip';
import {red300} from 'material-ui/styles/colors';
import Divider from 'material-ui/Divider';
import Clearfix from './Clearfix.jsx';

const styles = {
  chip: {
    margin: 4,
  },
  wrapper: {
    display: 'flex',
    flexWrap: 'wrap',
  },
  weekdayStyle: {
	float:'left',
	fontSize: '30px',
	fontWeight: 'bold',
	/* Safari */
	WebkitTransform: 'rotate(-90deg)',
	/* Firefox */
	MozTransform: 'rotate(-90deg)',
	/* IE */
	msTransform: 'rotate(-90deg)',
	/* Opera */
	OTransform: 'rotate(-90deg)',
	/* Internet Explorer */
	filter: 'progid:DXImageTransform.Microsoft.BasicImage(rotation=3)',
  },
  cardTextStyle: {
	fontWeight: 100,
  },
  buttonStyle: {
	marginRight: 0,
	float: 'right',
	opacity: 0.6,
	cursor: 'pointer',
  }
};

class Menuitems extends React.Component {
	constructor (props) {
		super(props);
		this.state = {};
		this.mouseOverButton = this.mouseOverButton.bind(this);
		this.mouseOutButton = this.mouseOutButton.bind(this);
	}
	mouseOverButton (e) {
		console.log('mouse over');

		// TODO

	}
	mouseOutButton (e) {
		console.log('mouse out');
		// e.target.style.width = this.imageState.width + 'px';
		// e.target.style.height = this.imageState.height + 'px';
		console.log('e.target', e.target);

		// TODO
	}
	findValueDeals (items) {
		// TODO


	}
	render () {

		// if (!this.props.items) {
		// 	return null;
		// }

		// this.findValueDeals(this.props.items.menu);

		// console.log('this.props.items.menu', this.props.items.menu);

		// let menuitems = {};
		// for (weekday in this.props.items.menu) {
		// 	if (!hasOwnProperty(this.props.items.menu[weekday])) {
		// 		continue;
		// 	}
		// 	menuitems[weekday] = this.props.items.menu[weekday].map((item) => (
		// 		<Menuitem
		// 			item={item}
		// 			isValueDeal={true}
		// 		/>)
		// 	);
		// }

		return (
			<Clearfix>
				<div style={styles.weekdayStyle}>
					Montag
				</div>
				<Card style={{width:'45%',float:'right'}}>
				    <CardHeader
				      title="Without Avatar"
				      subtitle="3,90 EUR"
				      actAsExpander={false}
				    >
						<FloatingActionButton
						  onMouseOver={this.mouseOverButton}
						  onMouseOut={this.mouseOutButton}
						  style={styles.buttonStyle}
						>
							<img src="img/dinner-test.jpg" />
					    </FloatingActionButton>
				    </CardHeader>
				    <CardText style={styles.cardTextStyle}>
						<div style={styles.wrapper}>
					        <Chip style={styles.chip}>Bauern Suppe</Chip>
					        <Chip style={styles.chip}>Brot</Chip>
					        <Chip style={styles.chip} backgroundColor={red300}>Vegetarian</Chip>
				        </div>
				    </CardText>
				</Card>
				<Card style={{width:'45%',float:'right'}}>
				    <CardHeader
				      title="Without Avatar"
				      subtitle="5,70 EUR"
				      actAsExpander={false}
				    >
						<FloatingActionButton
						  style={styles.buttonStyle}
						  onMouseOver={this.mouseOverButton}
						  onMouseOut={this.mouseOutButton}
						>
							<img src="img/dinner-test.jpg" />
					    </FloatingActionButton>
				    </CardHeader>
				    <CardText style={styles.cardTextStyle}>
						<div style={styles.wrapper}>
					        <Chip style={styles.chip}>Schildkröte</Chip>
					        <Chip style={styles.chip}>Suflaki</Chip>
				        </div>
				    </CardText>
				</Card>
				<Card style={{width:'45%',float:'right'}}>
				    <CardHeader
				      title="Without Avatar"
				      subtitle="5,70 EUR"
				      actAsExpander={false}
				    >
						<FloatingActionButton
						  style={styles.buttonStyle}
						  onMouseOver={this.mouseOverButton}
						  onMouseOut={this.mouseOutButton}
						>
							<img src="img/dinner-test.jpg" />
					    </FloatingActionButton>
				    </CardHeader>
				    <CardText style={styles.cardTextStyle}>
						<div style={styles.wrapper}>
					        <Chip style={styles.chip}>Schildkröte</Chip>
					        <Chip style={styles.chip}>Suflaki</Chip>
				        </div>
				    </CardText>
				</Card>
				<Card style={{width:'45%',float:'right'}}>
				    <CardHeader
				      title="Without Avatar"
				      subtitle="5,70 EUR"
				      actAsExpander={false}
				    >
						<FloatingActionButton
						  style={styles.buttonStyle}
						  onMouseOver={this.mouseOverButton}
						  onMouseOut={this.mouseOutButton}
						>
							<img src="img/dinner-test.jpg" />
					    </FloatingActionButton>
				    </CardHeader>
				    <CardText style={styles.cardTextStyle}>
						<div style={styles.wrapper}>
					        <Chip style={styles.chip}>Schildkröte</Chip>
					        <Chip style={styles.chip}>Suflaki</Chip>
				        </div>
				    </CardText>
				</Card>

				<Divider />

			</Clearfix>
		);
	}
}

export default Menuitems;
