import React, { useEffect, useState } from "react";
import { zodResolver } from "@hookform/resolvers/zod";
import { useForm, Controller } from "react-hook-form";
import { z } from "zod";
import { Label } from "@/components/ui/label";
import { Input } from "@/components/ui/input";
import { Loader2 } from "lucide-react";

import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectLabel,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import axios from "axios";
import { Button } from "@/components/ui/button";
import { useNavigate, useParams } from "react-router-dom";
import { useMutation, useQuery, useQueryClient } from "@tanstack/react-query";
import { toast } from "sonner";

const formSchema = z.object({
  email: z
    .string()
    .email("Invalid email address")
    .nonempty("Email is required"),
  password: z.string().min(6, "Password must be at least 6 characters"),
  name: z.string().min(2, "Name must be at least 2 characters"),
  mobile: z.coerce
    .number()
    .min(1000000000, {
      message: "Mobile number must be at least 10 digits long.",
    }) // Ensure the number is 10 digits or longer
    .max(9999999999, {
      message: "Mobile number must be exactly 10 digits long.",
    }), // Ensure the number is 10 digits or shorter
  role: z.string().min(1, "Role field is required"),
  active: z.coerce.number().optional(),
});

const Update = () => {
  const [isLoading, setIsLoading] = useState(false);
  const queryClient = useQueryClient();
  const { id } = useParams();
  const user = JSON.parse(localStorage.getItem("user"));
  const token = user.token;
  const navigate = useNavigate();

  const defaultValues = {
    email: "",
    password: "", // You can keep an empty string for password as it's meant for the user to change it
    name: "",
    mobile: "",
    role: "",
    active: "", // Make sure to match the expected value type (string)
  };

  const {
    control,
    handleSubmit,
    formState: { errors },
    setValue,
  } = useForm({ resolver: zodResolver(formSchema), defaultValues });

  const {
    data: editUser,
    isLoading: isEditUserDataLoading,
    isError: isEditUserDataError,
  } = useQuery({
    queryKey: ["editUser", id], // This is the query key
    queryFn: async () => {
      try {
        const response = await axios.get(
          `http://127.0.0.1:8000/api/profiles/${id}`,
          {
            headers: {
              "Content-Type": "application/json",
              Authorization: `Bearer ${token}`,
            },
          }
        );
        return response.data?.data; // Return the fetched data
      } catch (error) {
        throw new Error(error.message);
      }
    },
    keepPreviousData: true, // Keep previous data until the new data is available
  });

  useEffect(() => {
    if (editUser) {
      setValue("email", editUser.Profile?.email);
      setValue("name", editUser.Profile?.profile_name);
      setValue("mobile", editUser?.Profile?.mobile);
      setValue("password", editUser?.User?.password);

      setValue("role", editUser?.User?.role.name);
      setValue("active", String(editUser?.User?.active));
    }
  }, [editUser, setValue]);

  const updateMutation = useMutation({
    mutationFn: async (data) => {
      const response = await axios.put(
        `http://127.0.0.1:8000/api/profiles/${id}`,
        data,
        {
          headers: {
            "Content-Type": "application/json",
            Authorization: `Bearer ${token}`, // Include the Bearer token
          },
        }
      );
      return response.data;
    },
    onSuccess: (data) => {
      queryClient.invalidateQueries("users");

      toast.success("User Updated Successfully");
      setIsLoading(false);
      navigate("/users");
    },
    onError: (error) => {
      setIsLoading(false);

      console.log("got error ", error);
    },
  });
  const onSubmit = (data) => {
    setIsLoading(true);
    updateMutation.mutate(data);
  };

  return (
    <>
      <div className="p-5">
        {/* breadcrumb start */}
        <div className=" mb-11 text-sm">
          <div className="flex items-center space-x-2 text-gray-700">
            <span className="">
              {/* Users */}
              <Button
                onClick={() => navigate("/users")}
                className="p-0 text-blue-500"
                variant="link"
              >
                Users
              </Button>
            </span>
            <span className="text-gray-400">/</span>
            <span className="dark:text-gray-500">Edit</span>
          </div>
        </div>
        {/* breadcrumb ends */}

        {/* form style strat */}
        <div className="px-5 pb-7 pt-1 w-full dark:bg-gray-800 bg-white shadow-lg border  rounded-md">
          <div className="w-full py-3 flex justify-start items-center">
            <h2 className="text-lg  font-normal">Update Users</h2>
          </div>
          {/* row starts */}
          <form onSubmit={handleSubmit(onSubmit)}>
            <div className="w-full mb-8 grid grid-cols-1 md:grid-cols-3 gap-7 md:gap-4">
              <div className="relative">
                <Label className="font-normal" htmlFor="name">
                  Name: <span className="text-red-500">*</span>
                </Label>
                <Controller
                  name="name"
                  control={control}
                  render={({ field }) => (
                    <Input
                      {...field}
                      id="name"
                      className="mt-1"
                      type="text"
                      placeholder="Enter name"
                    />
                  )}
                />
                {errors.name && (
                  <p className="absolute text-red-500 text-sm mt-1 left-0">
                    {errors.name.message}
                  </p>
                )}
              </div>

              <div className="relative">
                <Label className="font-normal" htmlFor="email">
                  Email: <span className="text-red-500">*</span>
                </Label>
                <Controller
                  name="email"
                  control={control}
                  render={({ field }) => (
                    <Input
                      {...field}
                      id="email"
                      className="mt-1"
                      type="email"
                      placeholder="Enter email"
                    />
                  )}
                />
                {errors.name && (
                  <p className="absolute text-red-500 text-sm mt-1 left-0">
                    {errors.email.message}
                  </p>
                )}
              </div>

              <div className="relative">
                <Label className="font-normal" htmlFor="password">
                  Password: <span className="text-red-500">*</span>
                </Label>
                <Controller
                  name="password"
                  control={control}
                  render={({ field }) => (
                    <Input
                      {...field}
                      id="password"
                      type="password"
                      className="mt-1"
                      placeholder="Enter password"
                    />
                  )}
                />
                {errors.password && (
                  <p className="absolute text-red-500 text-sm mt-1 left-0">
                    {errors.password.message}
                  </p>
                )}
              </div>
            </div>
            {/* row ends */}
            {/* row starts */}
            <div className="w-full grid grid-cols-1 md:grid-cols-3 gap-7 md:gap-4">
              <div className="relative">
                <Label className="font-normal" htmlFor="mobile">
                  Mobile:
                </Label>
                <Controller
                  name="mobile"
                  control={control}
                  render={({ field }) => (
                    <Input
                      {...field}
                      id="mobile"
                      className="mt-1"
                      type="number"
                      placeholder="Enter mobile"
                    />
                  )}
                />
                {errors.mobile && (
                  <p className="absolute text-red-500 text-sm mt-1 left-0">
                    {errors.mobile.message}
                  </p>
                )}
              </div>
              <div className="relative">
                <Label className="font-normal" htmlFor="role">
                  Role: <span className="text-red-500">*</span>
                </Label>
                <Controller
                  name="role"
                  control={control}
                  render={({ field }) => (
                    <Select value={field.value} onValueChange={field.onChange}>
                      <SelectTrigger className="mt-1">
                        <SelectValue placeholder="Select role" />
                      </SelectTrigger>
                      <SelectContent>
                        <SelectGroup>
                          <SelectLabel>Select role</SelectLabel>
                          <SelectItem value="admin">Admin</SelectItem>
                          <SelectItem value="member">Member</SelectItem>
                        </SelectGroup>
                      </SelectContent>
                    </Select>
                  )}
                />
                {errors.role && (
                  <p className="absolute text-red-500 text-sm mt-1 left-0">
                    {errors.role.message}
                  </p>
                )}
              </div>
              <div className="relative">
                <Label className="font-normal" htmlFor="active">
                  Active: <span className="text-red-500">*</span>
                </Label>
                <Controller
                  name="active"
                  control={control}
                  render={({ field }) => (
                    <Select value={field.value} onValueChange={field.onChange}>
                      <SelectTrigger className="mt-1">
                        <SelectValue placeholder="Select status" />
                      </SelectTrigger>
                      <SelectContent>
                        <SelectGroup>
                          <SelectLabel>Select Status</SelectLabel>
                          <SelectItem value={String(1)}>Active</SelectItem>
                          <SelectItem value={String(0)}>Inactive</SelectItem>
                        </SelectGroup>
                      </SelectContent>
                    </Select>
                  )}
                />
                {errors.active && (
                  <p className="absolute text-red-500 text-sm mt-1 left-0">
                    {errors.active.message}
                  </p>
                )}
              </div>
            </div>
            {/* row ends */}
            <div className="w-full gap-4 mt-4 flex justify-end items-center">
              <Button
                type="button"
                className=" shadow-xl dark:text-white bg-red-500 hover:bg-red-600"
                onClick={() => navigate("/users")}
              >
                Cancel
              </Button>

              <Button
                type="submit"
                disabled={isLoading}
                className="shadow-xl dark:text-white bg-green-500 hover:bg-green-600"
              >
                {isLoading ? (
                  <>
                    <Loader2 className="animate-spin mr-2" /> {/* Spinner */}
                    Submitting...
                  </>
                ) : (
                  "Submit"
                )}
              </Button>
            </div>
          </form>
        </div>
      </div>
    </>
  );
};

export default Update;
