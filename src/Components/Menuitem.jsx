import React from 'react';

import Radium from 'radium';

import {
  Callout,
  Button,
  Link,
  Persona,
  PersonaSize,
  PersonaPresence,
} from 'office-ui-fabric-react';

const styles = {
  chip: {
    margin: 4,
    cursor: 'pointer',
    backgroundColor: '#AA6060',
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
  },
  menuCardItem: {
    width: '45%',
    float: 'right',
    overflow: 'none',
    '@media (max-width: 800px)': {
        width: '90%'
    }
  },
};

const examplePersona = {
  imageUrl: '/img/dinner-test.jpg',
  imageInitials: 'AL',
  primaryText: 'Annie Lindqvist',
  secondaryText: 'Software Engineer',
  tertiaryText: 'In a meeting',
  optionalText: 'Available at 4:00pm'
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
          <div style={styles.menuCardItem}>
              <Persona
                  { ...examplePersona }
                  size={ PersonaSize.tiny }
                  presence={ PersonaPresence.offline }
                  hidePersonaDetails={ false }
              />
          </div>
    );
  }
}

export default Radium(Menuitem);
