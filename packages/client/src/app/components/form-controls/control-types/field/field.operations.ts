import { useSelector } from 'react-redux';
import type { Field } from '@editor/store/slices/layout/fields';
import { fieldSelectors } from '@editor/store/slices/layout/fields/fields.selectors';
import { rowSelectors } from '@editor/store/slices/layout/rows/rows.selectors';
import type { FieldType } from '@ff-client/types/fields';
import type {
  FieldProperty,
  Option,
  OptionCollection,
  OptionGroup,
} from '@ff-client/types/properties';

export const convertCartographToOptionsCollection = (
  cartograph: Array<Field[]>[],
  property: FieldProperty,
  findType: (value: string) => FieldType
): OptionCollection => {
  const fields = useSelector(fieldSelectors.all);

  const options: OptionCollection = [];

  cartograph.forEach((rows: Array<Field[]>) => {
    rows.forEach((row: Field[]) => {
      row
        .filter((field: Field) => {
          if (!property.implements) {
            return true;
          }

          const type = findType(field.typeClass);
          if (!type) {
            return false;
          }

          return property.implements.every((implementation) =>
            type.implements?.includes(implementation)
          );
        })
        .forEach((field: Field) => {
          if (field.properties?.layout) {
            options.push(addOptionGroup(fields, field));
          } else {
            options.push(addOption(field));
          }
        });
    });
  });

  return options;
};

const addOption = (field: Field): Option => ({
  value: field.uid,
  label: field.properties.label,
});

const addOptionGroup = (fields: Field[], field: Field): OptionGroup => {
  const nestedRows = useSelector(
    rowSelectors.inLayout({
      uid: field.properties.layout,
    })
  );

  const children: Option[] = [];

  nestedRows.forEach((nestedRow) => {
    fields
      .filter((field) => field.rowUid === nestedRow.uid)
      .forEach((field) => {
        children.push({
          value: field.uid,
          label: field.properties.label,
        });
      });
  });

  return {
    label: field.properties.label,
    children,
  };
};
