import React from 'react';

import ReactHeight from 'react-height/build/react-height.js';
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

        if (!this.props) {
         return null;
        }

        console.log('this.props', this.props);

        const menuitems = ["Montag", "Dienstag", "Mittwoch"].map((weekday) => {
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
                <div>
                    <div style={styles.weekdayStyle}>
                        {weekday}
                    </div>
                    {items}
                </div>
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
            <Clearfix style={styles.main}>
              {menuitems}
              <Divider />
            </Clearfix>
          </ReactHeight>
        );
    }
}

export default Menuitems;
