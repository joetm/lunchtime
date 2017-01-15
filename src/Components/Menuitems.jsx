import React from 'react';

import Radium from 'radium';

import ReactHeight from 'react-height/build/react-height.js';

import Menuitem from './Menuitem.jsx';
import Clearfix from './Clearfix.jsx';

const styles = {
  main: {
    marginTop: '10px',
    marginBottom: '10px',
  },
  weekdayStyle: {
    float:'left',
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
            console.log(weekday, 'items', items);
            return (
                <Clearfix style={styles.main}>
                    <div style={styles.weekdayStyle}>
                        {weekday}
                    </div>
                    {items}
                </Clearfix>
            );
        });

        // this.findValueDeals(this.props.items.menu);

        // console.log('this.props.items.menu', this.props.items.menu);

        // let menuitems = {};
        // for (weekday in this.props.items.menu) {
        //  if (!hasOwnProperty(this.props.items.menu[weekday])) {
        //      continue;
        //  }
        //  menuitems[weekday] = this.props.items.menu[weekday].map((item) => (
        //      <Menuitem
        //          item={item}
        //          isValueDeal={true}
        //      />)
        //  );
        // }

        return (
          <ReactHeight onHeightReady={height => console.log(height)}>
              {menuitems}
          </ReactHeight>
        );
    }
}

export default Radium(Menuitems);
