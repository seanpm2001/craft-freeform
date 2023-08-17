import React from 'react';
import { useSelector } from 'react-redux';
import { Dropdown } from '@components/elements/custom-dropdown/dropdown';
import { Control } from '@components/form-controls/control';
import type { ControlType } from '@components/form-controls/types';
import { layoutSelectors } from '@editor/store/slices/layout/layouts/layouts.selectors';
import { useFieldTypeSearch } from '@ff-client/queries/field-types';
import { type FieldProperty } from '@ff-client/types/properties';

import { convertCartographToOptionsCollection } from './field.operations';

const Field: React.FC<ControlType<FieldProperty>> = ({
  value,
  property,
  errors,
  updateValue,
}) => {
  const cartograph = useSelector(layoutSelectors.cartographed.fullLayoutList);

  const findType = useFieldTypeSearch();

  const options = convertCartographToOptionsCollection(
    cartograph,
    property,
    findType
  );

  return (
    <Control property={property} errors={errors}>
      <Dropdown
        onChange={updateValue}
        value={value}
        options={options}
        emptyOption={property.emptyOption}
      />
    </Control>
  );
};

export default Field;
