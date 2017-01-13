import React from 'react';

class LoadingAnim extends React.PureComponent {

    render() {

		let isVisible = this.props.loading === true ?
	      {display: 'inline-block'} :
	      {display: 'none'};

        return (
            <div style={isVisible} title="...loading...">
            	LOADING
            </div>
        )
    }

}

export default LoadingAnim;
