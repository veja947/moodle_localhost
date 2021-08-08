import React from "react";
import {Option} from "rc-select";

export default class CampaignSelectorOption extends React.Component {
    constructor(props) {
        super(props);
    }

    render() {
        return (
            <Option value={this.props.id}>{this.props.name}</Option>
        );
    }
}