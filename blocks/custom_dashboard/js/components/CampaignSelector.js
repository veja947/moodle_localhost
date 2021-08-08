import React from "react";
import {Option} from "rc-select";
import {Select} from "antd";

const defaultOptions = [];

export default class CampaignSelector extends React.Component {
    constructor(props) {
        super(props);

        this.options = props.options;
    }

    onChange(value) {
        console.log(`CampaignSelector selected ${value}`);
    }

    onBlur() {
        console.log('CampaignSelector blur');
    }

    onFocus() {
        console.log('CampaignSelector focus');
    }

    onSearch(val) {
        console.log('CampaignSelector search:', val);
    }

    getAllOptions() {
        const options = this.options;
        let results = [];
        for (const [key, value] of Object.entries(options)) {
            results.push(<Option value={key}>{value}</Option>);
        }

        return results;
    }

    render() {
        return (
            <Select
                showSearch
                allowClear
                style={{ width: 200 }}
                placeholder="All compaigns in progress"
                optionFilterProp="children"
                onChange={this.onChange}
                onFocus={this.onFocus}
                onBlur={this.onBlur}
                onSearch={this.onSearch}
                filterOption={(input, option) =>
                    option.children.toLowerCase().indexOf(input.toLowerCase()) >= 0
                }
            >
                {this.getAllOptions()}
            </Select>
        );
    }
}