import { createRoot } from "react-dom/client";
import V2Button from "@/components/ui/v2/V2Button";
import V2Card from "@/components/ui/v2/V2Card";
import V2Input from "@/components/ui/v2/V2Input";
import V2Textarea from "@/components/ui/v2/V2Textarea";
import V2Select from "@/components/ui/v2/V2Select";
import V2Checkbox from "@/components/ui/v2/V2Checkbox";
import V2Checkboxes from "@/components/ui/v2/V2Checkboxes";
import V2TextBadge from "@/components/ui/v2/V2TextBadge";

const app = document.getElementById("app-playground");
if (app) {
  const App = () => {
    const components = [
      {
        name: "V2Button",
        show: true,
        render: (p: Props) => <V2Button {...p}>ボタン</V2Button>,
        condition: {
          variant: [
            "sub",
            "subOutline",
            "primary",
            "primaryOutline",
            "danger",
            "dangerOutline",
            "warn",
            "warnOutline",
            "info",
            "infoOutline",
            "success",
            "successOutline",
          ],
          disabled: [false, true],
        },
      },
      {
        name: "V2Button (size)",
        show: true,
        render: (p: Props) => <V2Button {...p}>ボタン</V2Button>,
        condition: {
          size: ["sm", "md", "lg"],
        },
      },
      {
        name: "V2Card",
        show: true,
        render: (p: Props) => (
          <V2Card {...p}>
            <h3>カード</h3>
            <div>これはV2Cardコンポーネントの内容です。</div>
          </V2Card>
        ),
        condition: {
          variant: [
            "main",
            "secondary",
            "primary",
            "danger",
            "warn",
            "info",
            "success",
          ],
        },
      },
      {
        name: "v2Input",
        show: true,
        render: (p: Props) => <V2Input value="test" {...p} />,
        condition: {
          disabled: [false, true],
        },
      },
      {
        name: "v2Input (required)",
        show: true,
        render: (p: Props) => <V2Input {...p} />,
        condition: {
          required: [true],
        },
      },
      {
        name: "v2Input (type)",
        show: true,
        render: (p: Props) => <V2Input {...p} />,
        condition: {
          type: [
            "text",
            "search",
            "email",
            "url",
            "password",
            "datetime-local",
            "file",
          ],
        },
      },
      {
        name: "v2Textarea",
        show: true,
        render: (p: Props) => <V2Textarea value={"test\ntest"} {...p} />,
        condition: {
          disabled: [false, true],
        },
      },
      {
        name: "v2Textarea (required)",
        show: true,
        render: (p: Props) => <V2Textarea {...p} />,
        condition: {
          required: [true],
        },
      },
      {
        name: "v2Select",
        show: true,
        render: (p: Props) => (
          <V2Select
            options={{ option1: "Option 1", option2: "Option 2" }}
            {...p}
          />
        ),
        condition: {
          disabled: [false, true],
        },
      },
      {
        name: "v2Select (required)",
        show: true,
        render: (p: Props) => (
          <V2Select
            options={{ "": "none", option1: "Option 1", option2: "Option 2" }}
            {...p}
          />
        ),
        condition: {
          required: [true],
        },
      },
      {
        name: "v2Checkbox",
        show: true,
        render: (p: Props, index: number) => (
          <V2Checkbox id={`c-${index}`} {...p}>
            チェックボックスのラベル
          </V2Checkbox>
        ),
        condition: {
          disabled: [false, true],
          checked: [false, true],
        },
      },
      {
        name: "v2Checkbox (required)",
        show: true,
        render: (p: Props, index: number) => (
          <V2Checkbox id={`c-${index}`} {...p}>
            チェックボックスのラベル
          </V2Checkbox>
        ),
        condition: {
          required: [true],
        },
      },
      {
        name: "v2Checkboxes",
        show: true,
        render: (p: Props, index: number) => (
          <V2Checkboxes
            id={`c-${index}`}
            {...p}
            options={{
              option1: "Option 1",
              option2: "Option 2",
              option3: "Option 3",
              option4: "Option 4",
              option5: "Option 5",
              option6: "Option 6",
              option7: "Option 7",
              option8: "Option 8",
              option9: "Option 9",
              option10: "Option 10",
              option11: "Option 11",
              option12: "Option 12",
              option13: "Option 13",
              option14: "Option 14",
              option15: "Option 15",
              option16: "Option 16",
              option17: "Option 17",
              option18: "Option 18",
              option19: "Option 19",
              option20: "Option 20",
            }}
            checkedOptions={["option1", "option3", "option5"]}
          />
        ),
        condition: {},
      },
      {
        name: "v2badge",
        show: true,
        render: (p: Props) => <V2TextBadge {...p}>バッジ</V2TextBadge>,
        condition: {
          variant: [
            "main",
            "sub",
            "primary",
            "danger",
            "warn",
            "info",
            "success",
          ],
        },
      },
      {
        name: "table",
        show: true,
        render: (p: Props) => (
          <table className="v2-table" {...p}>
            <thead>
              <tr>
                <th>Header 1</th>
                <th>Header 2</th>
                <th>Header 3</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>Data 1</td>
                <td>Data 2</td>
                <td>Data 3</td>
              </tr>
            </tbody>
          </table>
        ),
        condition: {},
      },
      {
        name: "table vertical header",
        show: true,
        render: (p: Props) => (
          <div className="v2-table-wrapper">
            <table className="v2-table" {...p}>
              <tbody>
                <tr>
                  <th>Header 1</th>
                  <td>Data 1</td>
                  <td>Data 1</td>
                  <td>Data 1</td>
                  <td>Data 1</td>
                  <td>Data 1</td>
                  <td>Data 1</td>
                </tr>
                <tr>
                  <th>Header 2</th>
                  <td>Data 2</td>
                  <td>Data 2</td>
                  <td>Data 2</td>
                  <td>Data 2</td>
                  <td>Data 2</td>
                  <td>Data 2</td>
                </tr>
              </tbody>
            </table>
          </div>
        ),
        condition: {},
      },
      {
        name: "text",
        show: true,
        render: (p: Props) => <div {...p}>テスト</div>,
        condition: {
          className: [
            ...["v2-text-h1", "v2-text-h2", "v2-text-h3", "v2-text-h4"],
            ...["v2-text-caption"],
          ],
        },
      },
      {
        name: "link",
        show: true,
        render: (p: Props) => (
          <a className="v2-link" {...p}>
            {String(p.href) || "リンクテキスト"}
          </a>
        ),
        condition: {
          href: [
            "#",
            "https://example.com",
            "https://simutrans-portal.128-bit.net",
            "http://localhost:1080",
          ],
        },
      },
    ];

    const filteredComponents = components
      .filter((c) => c.show)
      .map((c) => ({ ...c, patterns: generateAllPatterns(c.condition) }));

    return (
      <div className="flex flex-col gap-8">
        <div>
          {filteredComponents.map((component, componentIndex) => (
            <div key={componentIndex} className="mb-2">
              <a
                href={`#component-${componentIndex}`}
                className="link-internal block"
              >
                {component.name}
              </a>
              <div className="ml-4 space-x-2">
                {component.patterns.map((pattern, patternIndex) => (
                  <a
                    key={patternIndex}
                    href={`#component-${componentIndex}-pattern-${patternIndex}`}
                    className="link-internal inline-block"
                  >
                    {pattern.name}
                  </a>
                ))}
              </div>
            </div>
          ))}
        </div>
        <div className="flex flex-col gap-4">
          {filteredComponents.map((component, componentIndex) => {
            return (
              <div key={componentIndex}>
                <a
                  id={`component-${componentIndex}`}
                  href={`#component-${componentIndex}`}
                  className="mb-4"
                >
                  <p className="font-bold">{component.name}</p>
                </a>
                {component.patterns.map((pattern, patternIndex) => {
                  return (
                    <div key={patternIndex} className="mb-4">
                      <a
                        id={`component-${componentIndex}-pattern-${patternIndex}`}
                        href={`#component-${componentIndex}-pattern-${patternIndex}`}
                      >
                        <p>{pattern.name}</p>
                      </a>
                      {component.render(pattern.props, patternIndex)}
                    </div>
                  );
                })}
              </div>
            );
          })}
        </div>
      </div>
    );
  };

  createRoot(app).render(<App />);
}

type Condition = Record<string, unknown[] | undefined>;
type Pattern<T extends React.ElementType> = {
  name: string;
  props: React.ComponentProps<T>;
};
type Props = Record<string, unknown>;

function generateAllPatterns<T extends React.ElementType>(
  conditions: Condition
): Pattern<T>[] {
  const keys = Object.keys(conditions).filter(
    (key) => conditions[key] !== undefined
  );
  const patterns: Pattern<T>[] = [];

  function helper(
    index: number,
    currentProps: Record<string, unknown>,
    nameParts: string[]
  ) {
    if (index === keys.length) {
      patterns.push({
        name: nameParts.join(", "),
        props: currentProps as React.ComponentProps<T>,
      });
      return;
    }

    const key = keys[index];
    const values = conditions[key];

    if (!values) return;

    for (const value of values) {
      const newProps = { ...currentProps };
      const newNameParts = [...nameParts];

      if (value !== null) {
        newProps[key] = value;
        newNameParts.push(`${key}=${String(value)}`);
      } else {
        newNameParts.push(`${key}=null`);
      }

      helper(index + 1, newProps, newNameParts);
    }
  }

  helper(0, {}, []);
  return patterns;
}
