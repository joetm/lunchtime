import React from 'react';

import Radium from 'radium';

import Divider from 'material-ui/Divider';

import Menuitem from './Menuitem.jsx';
import Clearfix from './Clearfix.jsx';

const styles = {
  main: {
    marginTop: '10px',
    marginBottom: '10px',
  },
  weekdayStyle: {
    float:'left',
    width: '10%',
    fontSize: '30px',
    fontWeight: 'bold',
    /* Safari */
    WebkitTransform: 'translateY(100px) rotate(-90deg)',
    /* Firefox */
    MozTransform: 'translateY(100px) rotate(-90deg)',
    /* IE */
    msTransform: 'translateY(100px) rotate(-90deg)',
    /* Opera */
    OTransform: 'translateY(100px) rotate(-90deg)',
    /* Internet Explorer */
    filter: 'progid:DXImageTransform.Microsoft.BasicImage(rotation=3)',
  },
  weekdayMenuStyle: {
      float: 'right',
      width: '90%',
  },
};

class Menuitems extends React.Component {

    constructor (props) {
        super(props);
        this.state = {};
    }

    findValueDeals (items) {
        // TODO


    }

    render () {

        console.log('this.props', this.props);

        const weekdays = Object.keys(this.props);
        console.log('weekdays', weekdays);

        if (!this.props || !weekdays.length) {
            return null;
        }

        const menuitems = weekdays.map((weekday) => {
            if (this.props[weekday] === undefined) {
                return;
            }
            const items = this.props[weekday].map((item, index) => (
                <Menuitem
                    weekday={weekday}
                    {...this.props[weekday][index]}
                />
            ));
            // console.log(weekday, 'items', items);
            return (
                <Clearfix style={styles.main}>
                    <div style={styles.weekdayStyle}>
                        {weekday}
                    </div>
                    <div style={styles.weekdayMenuStyle}>
                        {items}
                    </div>
                    <Divider />
                </Clearfix>
            );
        });

        return (
            <Clearfix style={styles.main}>
              {menuitems}
            </Clearfix>
        );
    }
}

export default Radium(Menuitems);
