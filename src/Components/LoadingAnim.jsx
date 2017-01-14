import React from 'react';

import LoadingIcon from "material-ui/svg-icons/image/blur-circular";

const styles = {
    width: '100%',
    height: '100%',
    marginRight: 'auto',
    marginLeft: 'auto',
    padding: 0,
    animation: 'spinner 1.6s linear infinite'
};

class LoadingAnim extends React.PureComponent {
    componentDidMount() {
        // see https://davidwalsh.name/add-rules-stylesheets
        // create a style element
        const styleSheet = (function() {
            // Create the <style> tag
            let style = document.createElement("style");
            // Add a media (and/or media query) here if you'd like!
            // style.setAttribute("media", "screen")
            // style.setAttribute("media", "only screen and (max-width : 1024px)")
            // WebKit hack :(
            style.appendChild(document.createTextNode(""));
            // Add the <style> element to the page
            document.head.appendChild(style);
            return style.sheet;
        })();
        // 360 degree rotation animation
        const keyframes = `@keyframes spinner {
          to {transform: rotate(360deg);}
        }`;
        // add the rotation to the stylesheet
        styleSheet.insertRule(keyframes, styleSheet.cssRules.length);
    }
    render() {

		let isVisible = this.props.loading === true ?
	      {display: 'inline-block'} :
	      {display: 'none'};

        return (
            <div style={isVisible} title="...loading...">
            	<LoadingIcon style={styles} />
            </div>
        )
    }
}

export default LoadingAnim;
