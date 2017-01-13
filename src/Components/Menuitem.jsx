import React from 'react';

import {Card, CardActions, CardHeader, CardMedia, CardTitle, CardText} from 'material-ui/Card';
import FlatButton from 'material-ui/FlatButton';

const veggieWarning = () => (
	<div>VEGGIE!</div>
);

const Menuitem = (props) => (
  <Card>
    <CardTitle title={props.item.description} subtitle={props.vegetarian ? 'VEGGIE' : 'NON-VEGGIE'} />
  </Card>
);

export default Menuitem;
