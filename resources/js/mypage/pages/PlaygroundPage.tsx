import { createRoot } from "react-dom/client";
import V2Button from "@/components/ui/v2/V2Button";
import V2Card from "@/components/ui/v2/V2Card";
import V2Input from "@/components/ui/v2/V2Input";
import V2Textarea from "@/components/ui/v2/V2Textarea";

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
            "main",
            "mainOutline",
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
                      {component.render(pattern.props)}
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
