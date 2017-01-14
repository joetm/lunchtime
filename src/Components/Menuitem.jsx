import React from 'react';

import {Card, CardActions, CardHeader, CardMedia, CardTitle, CardText} from 'material-ui/Card';
import FlatButton from 'material-ui/FlatButton';

const Price = (props) => (
	<div>{props.price} EUR</div>
);

const Menuitem = (props) => (
  <Card>
    <CardTitle title={props.item.description} subtitle={props.item.vegetarian ? 'VEGGIE' : 'NON-VEGGIE'} />
    <div>
	    <Price price={props.item.price} />
	    {props.isValueDeal ? 'Value Deal': null}
	    <div>
	    	Tags: {props.item.words.join(", ")}
        </div>
    </div>
  </Card>
);

export default Menuitem;
