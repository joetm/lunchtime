import React from 'react';

import Radium from 'radium';

import {Card, CardActions, CardHeader, CardText} from 'material-ui/Card';
import FloatingActionButton from 'material-ui/FloatingActionButton';
import Avatar from 'material-ui/Avatar';
import Chip from 'material-ui/Chip';
import {red300} from 'material-ui/styles/colors';

const styles = {
  chip: {
    margin: '4px 4px 4px 0',
    cursor: 'pointer',
    backgroundColor: '#EEEEEE',
    border: '1px solid #AAAAAA',
  },
  tagWrapper: {
    display: 'flex',
    flexWrap: 'wrap',
  },
  cardTextStyle: {
    fontWeight: 100,
  },
  imgStyle: {
    marginRight: 0,
    float: 'right',
    opacity: 0.6,
    cursor: 'pointer',
  },
  menuCardItem: {
    width: '100%',
    float: 'right',
    overflow: 'none',
    '@media (max-width: 800px)': {
        width: '100%'
    }
  },
};


class Menuitem extends React.Component {
    constructor (props) {
        super(props);
        this.state = {};
        // take this!
        this.mouseOverButton = this.mouseOverButton.bind(this);
        this.mouseOutButton = this.mouseOutButton.bind(this);
        this._onCalloutDismiss = this._onCalloutDismiss.bind(this);
    }
    _onCalloutDismiss() {
        this.setState({
          isCalloutVisible: false
        });
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
          <Card
              style={styles.menuCardItem}
          >
              <CardHeader
                title={this.props.description}
                subtitle={this.formatPrice(this.props.price)}
                actAsExpander={false}
              >
                  <Avatar
                    src="img/dinner-test.jpg"
                    size={110}
                    style={styles.imgStyle}
                  />
              </CardHeader>
              <CardText
                  style={styles.cardTextStyle}
              >
                  <div
                      style={styles.tagWrapper}
                  >
                      {
                        this.props.words.map((tag) => (
                            <Chip style={styles.chip}>{tag}</Chip>
                        ))
                      }
                      {
                        this.props.vegetarian ?
                            <Chip style={styles.chip} backgroundColor={red300}>
                                Vegetarian
                            </Chip>
                        : null
                      }
                  </div>
              </CardText>
          </Card>
    );
  }
}

export default Radium(Menuitem);
