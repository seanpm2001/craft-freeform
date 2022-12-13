import type { ComponentType } from 'react';
import React, { Suspense } from 'react';
import { useAppDispatch } from '@editor/store';
import type { Field } from '@editor/store/slices/fields';
import type { Property, PropertyType } from '@ff-client/types/properties';

import { ErrorBoundary } from '../boundaries/ErrorBoundary';
import * as ControlTypes from '../form-controls';
import type { ControlType } from '../form-controls/types';

type Props = {
  field: Field;
  property: Property;
};

const types: { [key in PropertyType]?: ComponentType<ControlType> } =
  ControlTypes;

export const EditableComponent: React.FC<Props> = ({ field, property }) => {
  const dispatch = useAppDispatch();

  const FormControl = types[property.type];
  if (FormControl === undefined) {
    return <div>{`...${property.handle} <${property.type}>`}</div>;
  }

  FormControl.displayName = `Field <${property.type}>`;

  return (
    <ErrorBoundary message={`...${property.handle} <${property.type}>`}>
      <Suspense>
        <FormControl field={field} property={property} dispatch={dispatch} />
      </Suspense>
    </ErrorBoundary>
  );
};
