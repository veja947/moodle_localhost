import React from "react";
import {Switch} from "antd";

export default class TestCode extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            isHide : false,
        };
        this.handleToggle = this.handleToggle.bind(this);
    }

    handleToggle() {
        if (this.state.isHide)
        {
            $("#page-header").show();
            this.setState({isHide: false});
        } else {
            $("#page-header").hide();
            this.setState({isHide: true});
        }
    }

    render() {
        return (
            <div>
                <Switch checked={this.state.isHide} onChange={this.handleToggle} />
            </div>
        );
    }
}