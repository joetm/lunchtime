import React from 'react';

import {Card, CardActions, CardHeader, CardText} from 'material-ui/Card';
import FloatingActionButton from 'material-ui/FloatingActionButton';
import Chip from 'material-ui/Chip';
import {red300} from 'material-ui/styles/colors';

const styles = {
  chip: {
    margin: 4,
  },
  tagWrapper: {
    display: 'flex',
    flexWrap: 'wrap',
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


class Menuitem extends React.Component {
    constructor (props) {
        super(props);
        this.state = {};
        this.mouseOverButton = this.mouseOverButton.bind(this);
        this.mouseOutButton = this.mouseOutButton.bind(this);
    }
    formatPrice (price) {
      return `${price} EUR`;
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
    render () {
      console.log('this.props', this.props);
      return (
          <Card style={{width:'45%',float:'right'}}>
              <CardHeader
                title={this.props.description}
                subtitle={this.formatPrice(this.props.price)}
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
                  <div style={styles.tagWrapper}>
                      {
                        this.props.words.map((tag) => (
                            <Chip style={styles.chip}>{tag}</Chip>
                        ))
                      }
                      {
                        this.props.vegetarian ?
                            <Chip style={styles.chip} backgroundColor={red300}>Vegetarian</Chip>
                        : null
                      }
                  </div>
              </CardText>
          </Card>
    );
  }
}

export default Menuitem;
